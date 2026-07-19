<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Crr;
use App\Models\Customer;
use App\Models\Hub;
use App\Models\Office;
use App\Models\OtherCompany;
use App\Models\Shipment;
use App\Models\Supplier;
use Carbon\Carbon;

class ShipmentManifestPdfBuilder
{
    public function __construct(
        private ShipmentStockSnapshotService $stockSnapshotService,
    ) {}

    public function build(Shipment $shipment): array
    {
        $shipment->loadMissing([
            'accountManager.office',
            'creator',
        ]);

        $crrs = $this->stockSnapshotService->applyResolvedStockCrrs($shipment);
        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        $departureParty = $this->resolvePartyContact($shipment->departure, $partyNames);
        $consigneeParty = $this->resolvePartyContact($shipment->consignee, $partyNames);

        $packages = $crrs->flatMap(fn (Crr $crr) => $crr->packages);
        $totalPackages = $packages->count();
        $totalWeight = round((float) $packages->sum('weight'), 2);
        $totalCbm = round((float) $packages->sum('cbm'), 2);
        $totalCustomsValue = round((float) $crrs->sum('customs_value'), 2);
        $currency = $crrs->first()?->currency ?? 'USD';
        $volumeWeight = round($totalCbm * 167, 2);
        $totalCbft = round($totalCbm * 35.315, 2);

        $primaryVessel = $crrs->pluck('vessel_name')->filter()->first();
        $vesselInfo = $crrs
            ->map(fn (Crr $crr) => $crr->customerVessel)
            ->filter()
            ->first();

        $vesselLine = $primaryVessel ?? '—';
        if ($vesselInfo?->vessel_imo) {
            $vesselLine .= ' (IMO: ' . $vesselInfo->vessel_imo . ')';
        }
        $vesselLine .= $vesselInfo?->not_in_transit ? '' : ' in transit';

        $customerName = $crrs
            ->map(fn (Crr $crr) => $crr->customerVessel?->customer?->customer_name)
            ->filter()
            ->unique()
            ->implode(', ');

        $manifestRows = $crrs->map(function (Crr $crr) {
            $poNumbers = is_array($crr->po_numbers)
                ? implode(', ', $crr->po_numbers)
                : ($crr->po_numbers ?? '—');

            return [
                'vessel' => $crr->vessel_name ?? '—',
                'supplier' => $crr->supplier ?? '—',
                'po_number' => $poNumbers ?: '—',
                'items' => $crr->packages->count(),
                'weight' => round((float) $crr->packages->sum('weight'), 2),
                'cbm' => round((float) $crr->packages->sum('cbm'), 2),
                'customs_value' => number_format((float) ($crr->customs_value ?? 0), 2),
                'currency' => $crr->currency ?? 'USD',
                'description' => $crr->content ?? 'Shipspares',
                'stock_number' => $crr->stock_number ?? '—',
                'transit_id' => $crr->transit_id ?? '',
            ];
        });

        $packingRows = [];
        $packageIndex = 0;
        foreach ($crrs as $crr) {
            $poNumbers = is_array($crr->po_numbers)
                ? implode(', ', $crr->po_numbers)
                : ($crr->po_numbers ?? '—');

            if ($crr->packages->isEmpty()) {
                $packageIndex++;
                $packingRows[] = [
                    'stock_number' => $crr->stock_number ?? '—',
                    'position' => $crr->hub_code ?? '—',
                    'supplier' => $crr->supplier ?? '—',
                    'po_number' => $poNumbers ?: '—',
                    'item_label' => $packageIndex . ' of ' . max($totalPackages, 1),
                    'weight_label' => '0 of ' . round($totalWeight, 0) . ' kg',
                    'dimensions' => '—',
                    'pending_eta' => $crr->expected_delivery_date
                        ? Carbon::parse($crr->expected_delivery_date)->format('d.m.Y')
                        : null,
                    'label_code' => strtoupper($crr->hub_code ?? 'MTL') . '-' . ($crr->stock_number ?? $packageIndex) . '-1',
                ];
                continue;
            }

            foreach ($crr->packages as $package) {
                $packageIndex++;
                $packingRows[] = [
                    'stock_number' => $crr->stock_number ?? '—',
                    'position' => $package->warehouse_location ?: ($crr->hub_code ?? '—'),
                    'supplier' => $crr->supplier ?? '—',
                    'po_number' => $poNumbers ?: '—',
                    'item_label' => $packageIndex . ' of ' . max($totalPackages, 1),
                    'weight_label' => round((float) $package->weight, 0) . ' of ' . round($totalWeight, 0) . ' kg',
                    'dimensions' => $this->formatDimensions($package->length, $package->width, $package->height),
                    'pending_eta' => $crr->expected_delivery_date
                        ? Carbon::parse($crr->expected_delivery_date)->format('d.m.Y')
                        : null,
                    'label_code' => strtoupper($crr->hub_code ?? 'MTL') . '-' . ($crr->stock_number ?? $packageIndex) . '-' . $packageIndex,
                ];
            }
        }

        $createdAt = Carbon::now('UTC')->format('d.m.Y H:i') . ' UTC';
        $handledBy = trim(($shipment->creator?->name ?? 'System') . ' on ' . Carbon::now()->format('d.m.Y H:i'));

        $companyName = $departureParty['name'] ?: 'Marinetrans';
        $companyAddress = $departureParty['address_line'] ?: '—';
        $companyPhone = $departureParty['phone'] ?: '—';
        $companyEmail = $departureParty['email'] ?: '—';

        $invoiceEmail = $departureParty['invoice_email'] ?? $companyEmail;

        return [
            'shipment' => $shipment,
            'titleLine' => trim(($shipment->service ?? 'Shipment') . ' ' . $shipment->shipment_number),
            'companyName' => $companyName,
            'companyAddress' => $companyAddress,
            'companyPhone' => $companyPhone,
            'companyEmail' => $companyEmail,
            'createdAt' => $createdAt,
            'shippedThrough' => $this->formatContactLine($departureParty),
            'invoiceTo' => $this->formatContactLine(array_merge($departureParty, [
                'email' => $invoiceEmail,
            ])),
            'consigneeName' => $consigneeParty['name'] ?: ($shipment->consignee_att ?? '—'),
            'consigneeAddress' => $this->formatShipmentAddress($shipment),
            'consigneePhone' => $shipment->consignee_att ? '' : '',
            'consigneeEmail' => $shipment->consignee_email ?? '',
            'consigneeContact' => $shipment->consignee_att ?? '—',
            'consigneeContactEmail' => $shipment->consignee_email ?? '—',
            'consigneeContactPhone' => $shipment->location ?? '',
            'agentName' => $consigneeParty['name'] ?: '—',
            'vesselLine' => $vesselLine,
            'departurePort' => $this->joinParts([
                $shipment->departure_port_code,
                $departureParty['name'] ?? null,
            ], ' - ') ?: '—',
            'destinationPort' => $this->formatPortLabel($shipment->consignee_port_code, $shipment->location),
            'documentHandledBy' => $handledBy,
            'serviceLabel' => $shipment->service ?? '—',
            'additionalServiceLabel' => $shipment->additional_service ?: '—',
            'pcsSummary' => $totalPackages . ' / ' . $totalPackages . ' / ' . $totalWeight . ' kg',
            'deadlineArrival' => $shipment->deadline_arrival?->format('d.m.y') ?? '—',
            'commentsHub' => $shipment->comments_departure_hub ?? '',
            'combinedPoReference' => '*' . $shipment->shipment_number . '*',
            'customerName' => $customerName ?: '—',
            'manifestRows' => $manifestRows,
            'packingRows' => $packingRows,
            'totals' => [
                'packages' => $totalPackages,
                'weight' => $totalWeight,
                'volume_weight' => $volumeWeight,
                'customs_value' => number_format($totalCustomsValue, 2),
                'currency' => $currency,
                'cbm' => $totalCbm,
                'cbft' => $totalCbft,
            ],
            'shipperLine' => $this->formatContactLine($departureParty),
            'consigneeLine' => $this->formatShipmentAddress($shipment, true),
        ];
    }

    private function resolvePartyContact(?string $composite, array $partyNames): array
    {
        $result = [
            'name' => '',
            'address_line' => '',
            'phone' => '',
            'email' => '',
            'invoice_email' => '',
            'port_code' => '',
        ];

        if (!$composite) {
            return $result;
        }

        if (!str_contains($composite, ':')) {
            $result['name'] = $partyNames[$composite] ?? $composite;

            return $result;
        }

        [$type, $id] = explode(':', $composite, 2);
        $id = (int) $id;
        $result['name'] = $partyNames[$composite] ?? $composite;

        switch ($type) {
            case 'hub':
                $hub = Hub::find($id);
                if ($hub) {
                    $result['name'] = $hub->hub_name;
                    $result['address_line'] = $this->joinParts([
                        $hub->hub_address,
                        $hub->city,
                        $hub->zip_code,
                        $hub->country,
                    ]);
                    $result['phone'] = $hub->phone_number;
                    $result['email'] = $hub->email;
                    $result['invoice_email'] = $hub->emails_for_invoicing ?: $hub->email;
                    $result['port_code'] = $hub->port_code;
                }
                break;
            case 'agent':
                $agent = Agent::with('country')->find($id);
                if ($agent) {
                    $result['name'] = $agent->agent_name;
                    $result['address_line'] = $this->joinParts([
                        $agent->agent_address,
                        $agent->city,
                        $agent->zip_code,
                        $agent->country?->name,
                    ]);
                    $result['phone'] = $agent->phone;
                    $result['email'] = $agent->email;
                    $result['port_code'] = $agent->port_code;
                }
                break;
            case 'office':
                $office = Office::with('country')->find($id);
                if ($office) {
                    $result['name'] = $office->office_name;
                    $result['address_line'] = $this->joinParts([
                        $office->address,
                        $office->city,
                        $office->zip_code,
                        $office->country?->name,
                    ]);
                    $result['phone'] = $office->phone_number;
                    $result['email'] = $office->email;
                    $result['port_code'] = $office->port_code ?? '';
                }
                break;
            case 'customer':
                $customer = Customer::find($id);
                if ($customer) {
                    $result['name'] = $customer->customer_name;
                }
                break;
            case 'supplier':
                $supplier = Supplier::find($id);
                if ($supplier) {
                    $result['name'] = $supplier->supplier_name;
                    $result['address_line'] = $this->joinParts([
                        $supplier->street_address,
                        $supplier->city,
                        $supplier->zip_code,
                    ]);
                    $result['phone'] = $supplier->phone;
                    $result['email'] = $supplier->email;
                }
                break;
            case 'other_company':
                $company = OtherCompany::with('country')->find($id);
                if ($company) {
                    $result['name'] = $company->company_name;
                    $result['address_line'] = $this->joinParts([
                        $company->street_address,
                        $company->city,
                        $company->zip_code,
                        $company->country?->name,
                    ]);
                    $result['phone'] = $company->phone;
                    $result['email'] = $company->email;
                    $result['port_code'] = $company->port_code;
                }
                break;
        }

        return $result;
    }

    private function formatShipmentAddress(Shipment $shipment, bool $includePhone = false): string
    {
        $parts = array_filter([
            $shipment->consignee_address,
            $shipment->consignee_city,
            $shipment->consignee_district,
            $shipment->consignee_zip,
            $shipment->consignee_country,
        ]);

        $line = $this->joinParts($parts);

        if ($includePhone && $shipment->location) {
            $line .= ($line ? ', ' : '') . $shipment->location;
        }

        if ($shipment->consignee_email) {
            $line .= ($line ? ', ' : '') . $shipment->consignee_email;
        }

        return $line ?: '—';
    }

    private function formatContactLine(array $party): string
    {
        $segments = array_filter([
            $party['name'] ?? null,
            $party['address_line'] ?? null,
            trim(($party['phone'] ?? '') . ($party['email'] ? ', ' . $party['email'] : '')),
        ]);

        return $this->joinParts($segments) ?: '—';
    }

    private function formatPortLabel(?string $portCode, ?string $city, ?array $party = null, ?string $country = null): string
    {
        $parts = array_filter([
            $portCode,
            $city ?: ($party['name'] ?? null),
            $country,
        ]);

        return $this->joinParts($parts, ' / ') ?: '—';
    }

    private function formatDimensions($length, $width, $height): string
    {
        if (!$length && !$width && !$height) {
            return '—';
        }

        return implode(' / ', [
            $this->formatNumber($length),
            $this->formatNumber($width),
            $this->formatNumber($height),
        ]);
    }

    private function formatNumber($value): string
    {
        return $value !== null && $value !== '' ? (string) round((float) $value, 0) : '—';
    }

    private function joinParts(array $parts, string $separator = ', '): string
    {
        return collect($parts)
            ->map(fn ($part) => trim((string) $part))
            ->filter()
            ->implode($separator);
    }
}
