<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Crr;
use App\Models\CrrCost;
use App\Models\CrrDocument;
use App\Models\CrrPackage;
use App\Models\Customer;
use App\Models\Hub;
use App\Models\Office;
use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShipmentTransitStockDuplicationService
{
    /**
     * @return array<int, int> Map of original CRR id => duplicate CRR id
     */
    public function resolveConsigneePartyCode(?string $party): ?string
    {
        return $this->resolveHubAgentFromShipmentParty($party);
    }

    public function duplicateStocksForTransit(Shipment $shipment, ?string $consigneeCode = null): array
    {
        $shipment->loadMissing([
            'crrs.packages',
            'crrs.costs',
            'crrs.documents',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
        ]);

        $shipmentNumber = $shipment->shipment_number;
        $originalIds = $shipment->crrs->pluck('id')->all();

        $existingDuplicates = Crr::query()
            ->whereIn('duplicated_from_crr_id', $originalIds)
            ->where('internal_shipment', $shipmentNumber)
            ->get()
            ->keyBy('duplicated_from_crr_id');

        $mapping = [];

        foreach ($shipment->crrs as $original) {
            if ($existingDuplicates->has($original->id)) {
                $mapping[$original->id] = $existingDuplicates[$original->id]->id;

                continue;
            }

            $duplicate = $this->duplicateCrr($original, $shipment, $shipmentNumber, $consigneeCode);
            $mapping[$original->id] = $duplicate->id;
        }

        if (!empty($mapping)) {
            $shipment->crrs()->sync(array_values($mapping));
        }

        return $mapping;
    }

    private function duplicateCrr(Crr $original, Shipment $shipment, string $shipmentNumber, ?string $consigneeCode = null): Crr
    {
        $attributes = $original->only($original->getFillable());
        $attributes['internal_shipment'] = $shipmentNumber;
        $attributes['duplicated_from_crr_id'] = $original->id;
        $attributes['status'] = Crr::STATUS_ACTIVE;

        $transit = $this->resolveTransitFields($shipment);
        if ($transit['transit_type'] !== null) {
            $attributes['transit_type'] = $transit['transit_type'];
        }
        if ($transit['transit_id'] !== null) {
            $attributes['transit_id'] = $transit['transit_id'];
        }

        $hubAgent = $this->resolveHubAgentFromConsigneeCode($consigneeCode);
        if ($hubAgent === null || $hubAgent === '') {
            $hubAgent = (string) ($this->resolveHubAgentFromShipmentParty($shipment->consignee) ?? '');
        }
        if ($hubAgent !== '') {
            $attributes['hub_agent'] = $hubAgent;
        }

        $deliveryDate = $this->deliveryDateFromShipment($shipment);
        if ($deliveryDate !== null) {
            $attributes['expected_delivery_date'] = $deliveryDate;
            $attributes['actual_delivery_date'] = $deliveryDate;
        }

        $duplicate = Crr::create($attributes);

        foreach ($original->packages as $package) {
            $packageAttributes = $package->only($package->getFillable());
            $packageAttributes['warehouse_location'] = $shipmentNumber;
            $packageAttributes['crr_id'] = $duplicate->id;

            CrrPackage::create($packageAttributes);
        }

        foreach ($original->costs as $cost) {
            $costAttributes = $cost->only($cost->getFillable());
            $costAttributes['crr_id'] = $duplicate->id;

            CrrCost::create($costAttributes);
        }

        foreach ($original->documents as $document) {
            $this->duplicateDocument($document, $duplicate->id);
        }

        return $duplicate;
    }

    /**
     * @return array{transit_type: ?string, transit_id: ?string}
     */
    private function resolveTransitFields(Shipment $shipment): array
    {
        return match ($shipment->service) {
            'Courier' => [
                'transit_type' => 'CMR',
                'transit_id' => $this->firstNonEmptyValue($shipment->courierLegs->pluck('airway_bill')),
            ],
            'Airfreight' => [
                'transit_type' => 'AWB',
                'transit_id' => $this->firstNonEmptyValue($shipment->flights->pluck('leg_reference')),
            ],
            'Sea freight' => [
                'transit_type' => 'B/L',
                'transit_id' => $this->firstNonEmptyValue($shipment->seaLegs->pluck('bill_of_lading')),
            ],
            'Truck' => [
                'transit_type' => 'CMR',
                'transit_id' => $this->firstNonEmptyValue($shipment->truckLegs->pluck('cmr')),
            ],
            default => [
                'transit_type' => null,
                'transit_id' => null,
            ],
        };
    }

    private function firstNonEmptyValue($values): ?string
    {
        foreach ($values as $value) {
            $normalized = trim((string) ($value ?? ''));
            if ($normalized !== '') {
                return $normalized;
            }
        }

        return null;
    }

    private function resolveHubAgentFromConsigneeCode(?string $consigneeCode): ?string
    {
        $consigneeCode = trim((string) ($consigneeCode ?? ''));
        if ($consigneeCode === '') {
            return null;
        }

        $hub = Hub::query()
            ->where('code', $consigneeCode)
            ->orWhere('port_code', $consigneeCode)
            ->orWhere('un_locode', $consigneeCode)
            ->first();
        if ($hub) {
            return $this->hubAgentValue($hub);
        }

        $agent = Agent::query()
            ->where('code', $consigneeCode)
            ->orWhere('port_code', $consigneeCode)
            ->orWhere('un_locode', $consigneeCode)
            ->first();
        if ($agent) {
            return $this->hubAgentValue($agent);
        }

        $customer = Customer::query()
            ->where('customer_number', $consigneeCode)
            ->orWhere('un_locode', $consigneeCode)
            ->orWhereHas('addresses', function ($query) use ($consigneeCode) {
                $query->where('port_code', $consigneeCode);
            })
            ->first();
        if ($customer) {
            $customerNumber = trim((string) ($customer->customer_number ?? ''));
            if ($customerNumber !== '') {
                return $customerNumber;
            }

            $customerName = trim((string) ($customer->customer_name ?? ''));

            return $customerName !== '' ? $customerName : null;
        }

        return $consigneeCode;
    }

    private function deliveryDateFromShipment(Shipment $shipment): ?string
    {
        $deadlineArrival = $shipment->deadline_arrival;

        if ($deadlineArrival === null) {
            return null;
        }

        if ($deadlineArrival instanceof \DateTimeInterface) {
            return Carbon::instance($deadlineArrival)->format('Y-m-d');
        }

        $normalized = trim((string) $deadlineArrival);
        if ($normalized === '') {
            return null;
        }

        try {
            return Carbon::parse($normalized)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function resolveHubAgentFromShipmentParty(?string $party): ?string
    {
        if (!$party || !str_contains($party, ':')) {
            return null;
        }

        [$type, $id] = explode(':', $party, 2);
        $id = (int) $id;

        if ($id <= 0) {
            return null;
        }

        return match ($type) {
            'hub' => $this->hubAgentValue(Hub::find($id)),
            'agent' => $this->hubAgentValue(Agent::find($id)),
            'office' => $this->hubAgentValue(Office::find($id)),
            default => null,
        };
    }

    private function hubAgentValue(?object $party): ?string
    {
        if (!$party) {
            return null;
        }

        $code = trim((string) ($party->code ?? ''));
        if ($code !== '') {
            return $code;
        }

        $name = trim((string) ($party->hub_name ?? $party->agent_name ?? $party->office_name ?? ''));

        return $name !== '' ? $name : null;
    }

    private function duplicateDocument(CrrDocument $document, int $duplicateCrrId): void
    {
        $disk = \App\Support\PrivateDisk::disk();
        $newPath = $document->file_path;

        if ($document->file_path && $disk->exists($document->file_path)) {
            $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
            $basename = pathinfo($document->file_path, PATHINFO_FILENAME);
            $newPath = 'crr_documents/' . $basename . '-dup-' . Str::uuid() . ($extension ? '.' . $extension : '');

            try {
                $disk->copy($document->file_path, $newPath);
            } catch (\Throwable $e) {
                Log::warning('Could not copy CRR document during transit duplication: ' . $e->getMessage());
                $newPath = $document->file_path;
            }
        }

        CrrDocument::create([
            'crr_id' => $duplicateCrrId,
            'file_name' => $document->file_name,
            'file_path' => $newPath,
            'file_type' => $document->file_type,
        ]);
    }
}
