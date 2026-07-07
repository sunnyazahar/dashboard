<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentCourierLeg;
use App\Models\ShipmentFlight;
use App\Models\ShipmentHandCarryLeg;
use App\Models\ShipmentReleaseLeg;
use App\Models\ShipmentSeaLeg;
use App\Models\ShipmentTruckLeg;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PreAlertMailService
{
    public function __construct(
        private ShipmentManifestPdfBuilder $manifestPdfBuilder,
        private EmlMessageBuilder $emlMessageBuilder,
    ) {}

    public function buildEml(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null, array $documentIds = []): string
    {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail, $documentIds);

        return $this->emlMessageBuilder->build(
            $mail['senderName'],
            $mail['senderEmail'],
            $mail['to'],
            $mail['cc'],
            $mail['subject'],
            $mail['body'],
            $mail['attachments']
        );
    }

    public function buildPreview(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): array
    {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail);

        return [
            'to' => collect($mail['to'])->pluck('email')->filter()->implode(','),
            'cc' => collect($mail['cc'])->pluck('email')->filter()->implode(','),
            'subject' => $mail['subject'],
            'body' => preg_replace("/\r\n|\r|\n/", "\n", $mail['body']),
        ];
    }

    private function prepareMail(Shipment $shipment, ?string $senderName, ?string $senderEmail, array $documentIds = []): array
    {
        $shipment->loadMissing([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'documents',
            'preAlerts',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
        ]);

        if ($shipment->crrs->isEmpty()) {
            throw new RuntimeException('No stock items linked to this shipment.');
        }

        $manifestData = $this->manifestPdfBuilder->build($shipment);
        $partyNames = Shipment::batchResolvePartyNames(collect([$shipment]));
        $consigneeParty = $this->resolveConsigneeContact($shipment, $partyNames);

        $senderName = $senderName ?: ($shipment->creator?->name ?? 'Marinetrans');
        $senderEmail = $senderEmail ?: ($shipment->creator?->email ?? config('mail.from.address', 'esea@marinetrans.net'));

        return [
            'senderName' => $senderName,
            'senderEmail' => $senderEmail,
            'subject' => $this->buildSubject($shipment, $manifestData),
            'body' => $this->buildBody($shipment, $manifestData, $consigneeParty, $senderName, $senderEmail),
            'to' => $this->buildToAddresses($consigneeParty),
            'cc' => $this->buildCcAddresses($shipment, $senderEmail),
            'attachments' => $this->buildAttachments($shipment, $manifestData, $documentIds),
        ];
    }

    private function buildSubject(Shipment $shipment, array $manifestData): string
    {
        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $deadline = $shipment->deadline_arrival?->format('d.m.Y') ?? '—';
        $service = $shipment->service ?? '—';
        $departure = $manifestData['departurePort'] ?? '—';
        $destination = $manifestData['destinationPort'] ?? '—';

        return sprintf(
            'Pre-alert for %s / %s / %s / %s / MT REF: %s / From %s to %s',
            $shipment->shipment_number,
            $vessel,
            $deadline,
            $service,
            $shipment->shipment_number,
            $departure,
            $destination
        );
    }

    private function buildBody(
        Shipment $shipment,
        array $manifestData,
        array $consigneeParty,
        string $senderName,
        string $senderEmail
    ): string {
        $consigneeName = $consigneeParty['name'] ?: ($shipment->consignee_att ?: 'Sir/Madam');
        $destination = $manifestData['destinationPort'] ?? 'destination';
        $service = $shipment->service ?? 'shipment';
        $deadline = $shipment->deadline_arrival?->format('d.m.Y') ?? '—';

        $lines = [
            'To ' . $consigneeName,
            '',
            'Please find attached pre-alert for ' . $service . ' to ' . $destination,
            '',
            'Deadline ' . $deadline,
            '',
        ];

        if ($shipment->customer_reference) {
            $lines[] = 'As per quote ' . $shipment->customer_reference . ', pls check with agent for loading.';
            $lines[] = '';
        }

        $lines[] = 'MANIFEST DETAILS';
        $lines[] = 'Shipment: ' . $shipment->shipment_number;
        $lines[] = 'From: ' . ($manifestData['departurePort'] ?? '—');
        $lines[] = 'To: ' . ($manifestData['destinationPort'] ?? '—');
        $lines[] = 'Vessel: ' . ($manifestData['vesselLine'] ?? '—');
        $lines[] = 'Total packages: ' . ($manifestData['totals']['packages'] ?? '—');
        $lines[] = 'Total weight: ' . ($manifestData['totals']['weight'] ?? '—') . ' kg';
        $lines[] = 'Total CBM: ' . ($manifestData['totals']['cbm'] ?? '—');
        $lines[] = '';

        $serviceDetails = $this->buildServiceDetailsSection($shipment);
        if ($serviceDetails !== '') {
            $lines[] = $serviceDetails;
            $lines[] = '';
        }

        if ($shipment->comments_departure_hub) {
            $lines[] = $shipment->comments_departure_hub;
            $lines[] = '';
        } elseif ($shipment->comments_consignee) {
            $lines[] = $shipment->comments_consignee;
            $lines[] = '';
        }

        if ($shipment->special_considerations_destination) {
            $lines[] = $shipment->special_considerations_destination;
            $lines[] = '';
        }

        $lines[] = 'Pls keep us posted.';
        $lines[] = '';
        $lines[] = 'With kind regards,';
        $lines[] = $senderName;

        $senderPhone = $shipment->accountManager?->phone_number;
        if ($senderPhone) {
            $lines[] = $senderPhone;
        }

        $lines[] = $senderEmail;

        $companyName = $shipment->accountManager?->office?->office_name
            ?? $manifestData['companyName']
            ?? 'Marinetrans';

        $lines[] = $companyName;
        $lines[] = '';
        $lines[] = 'For all our subsidiaries, with the exception of Marinetrans Germany GmbH, following Terms & Conditions apply to all offered services: https://marinetrans.com/wp-content/uploads/2022/02/Marinetrans-General-Terms-Conditions-version-1.0-January-2022.pdf';

        return implode("\r\n", $lines);
    }

    private function buildServiceDetailsSection(Shipment $shipment): string
    {
        $service = $shipment->service;
        if (!$service) {
            return '';
        }

        $lines = ['SERVICE DETAILS', 'Service: ' . $service, ''];

        switch ($service) {
            case 'Airfreight':
                foreach ($shipment->flights as $index => $flight) {
                    $lines = array_merge($lines, $this->formatFlightLeg($flight, $index + 1));
                }
                break;
            case 'Sea freight':
                foreach ($shipment->seaLegs as $index => $leg) {
                    $lines = array_merge($lines, $this->formatSeaLeg($leg, $index + 1));
                }
                break;
            case 'Truck':
                foreach ($shipment->truckLegs as $index => $leg) {
                    $lines = array_merge($lines, $this->formatTruckLeg($leg, $index + 1));
                }
                break;
            case 'Courier':
                foreach ($shipment->courierLegs as $index => $leg) {
                    $lines = array_merge($lines, $this->formatCourierLeg($leg, $index + 1));
                }
                break;
            case 'Release':
                foreach ($shipment->releaseLegs as $index => $leg) {
                    $lines = array_merge($lines, $this->formatReleaseLeg($leg, $index + 1));
                }
                break;
            case 'Hand Carry':
                foreach ($shipment->handCarryLegs as $index => $leg) {
                    $lines = array_merge($lines, $this->formatHandCarryLeg($leg, $index + 1));
                }
                break;
        }

        return count($lines) > 3 ? implode("\r\n", $lines) : '';
    }

    /**
     * @return array<int, string>
     */
    private function formatFlightLeg(ShipmentFlight $flight, int $number): array
    {
        return $this->formatLegBlock('Flight ' . $number, [
            'Leg reference' => $flight->leg_reference,
            'Flight number' => $flight->flight_number,
            'Departure date' => $this->formatDate($flight->departure_date),
            'Arrival date' => $this->formatDate($flight->arrival_date),
            'Arrival time' => $flight->arrival_time,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function formatSeaLeg(ShipmentSeaLeg $leg, int $number): array
    {
        return $this->formatLegBlock('Sea leg ' . $number, [
            'Bill of lading' => $leg->bill_of_lading,
            'Container number' => $leg->container_number,
            'Transport vessel IMO' => $leg->transport_vessel_imo,
            'Transport vessel name' => $leg->transport_vessel_name,
            'ETD' => $this->formatDate($leg->etd),
            'ETA' => $this->formatDate($leg->eta),
            'Arrival time' => $leg->arrival_time,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function formatTruckLeg(ShipmentTruckLeg $leg, int $number): array
    {
        return $this->formatLegBlock('Truck ' . $number, [
            'CMR' => $leg->cmr,
            'Freight company' => $leg->freight_company,
            'Departure date' => $this->formatDate($leg->departure_date),
            'Arrival date' => $this->formatDate($leg->arrival_date),
            'Arrival time' => $leg->arrival_time,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function formatCourierLeg(ShipmentCourierLeg $leg, int $number): array
    {
        return $this->formatLegBlock('Courier ' . $number, [
            'Airway bill' => $leg->airway_bill,
            'Carrier' => $leg->carrier,
            'Departure date' => $this->formatDate($leg->departure_date),
            'Arrival date' => $this->formatDate($leg->arrival_date),
            'Arrival time' => $leg->arrival_time,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function formatReleaseLeg(ShipmentReleaseLeg $leg, int $number): array
    {
        return $this->formatLegBlock('Release ' . $number, [
            'Freight company' => $leg->freight_company,
            'Delivery date' => $this->formatDate($leg->delivery_date),
            'Delivery time' => $leg->delivery_time,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function formatHandCarryLeg(ShipmentHandCarryLeg $leg, int $number): array
    {
        return $this->formatLegBlock('Hand carry ' . $number, [
            'Departure date' => $this->formatDate($leg->departure_date),
            'Arrival date' => $this->formatDate($leg->arrival_date),
            'Arrival time' => $leg->arrival_time,
            'Contact name' => $leg->contact_name,
            'Contact phone' => $leg->contact_phone,
            'Onboard hand carry' => $leg->onboard_hand_carry ? 'Yes' : 'No',
        ]);
    }

    /**
     * @param  array<string, mixed>  $fields
     * @return array<int, string>
     */
    private function formatLegBlock(string $title, array $fields): array
    {
        $lines = [$title . ':'];
        $hasValue = false;

        foreach ($fields as $label => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $hasValue = true;
            $lines[] = '  ' . $label . ': ' . $value;
        }

        if (!$hasValue) {
            return [];
        }

        $lines[] = '';

        return $lines;
    }

    private function formatDate(mixed $date): ?string
    {
        if ($date === null) {
            return null;
        }

        if ($date instanceof \DateTimeInterface) {
            return $date->format('d.m.Y');
        }

        return (string) $date;
    }

    /**
     * @return array<int, array{name?: string, email: string}>
     */
    private function buildToAddresses(array $consigneeParty): array
    {
        $addresses = [];

        if (!empty($consigneeParty['email'])) {
            $addresses[] = [
                'name' => $consigneeParty['name'] ?? '',
                'email' => $consigneeParty['email'],
            ];
        }

        return $addresses;
    }

    /**
     * @return array<int, array{name?: string, email: string}>
     */
    private function buildCcAddresses(Shipment $shipment, string $senderEmail): array
    {
        $addresses = [];

        if ($shipment->accountManager?->email && $shipment->accountManager->email !== $senderEmail) {
            $addresses[] = [
                'name' => $shipment->accountManager->name,
                'email' => $shipment->accountManager->email,
            ];
        }

        if ($shipment->consignee_email && !collect($addresses)->contains('email', $shipment->consignee_email)) {
            $addresses[] = [
                'name' => $shipment->consignee_att ?? '',
                'email' => $shipment->consignee_email,
            ];
        }

        return $addresses;
    }

    /**
     * @return array<int, array{filename: string, content: string, mime: string}>
     */
    private function buildAttachments(Shipment $shipment, array $manifestData, array $documentIds = []): array
    {
        $attachments = [];

        $latestPreAlert = $shipment->preAlerts->sortByDesc('version')->first();
        if ($latestPreAlert && is_file(Storage::disk('public')->path($latestPreAlert->file_path))) {
            $attachments[] = [
                'filename' => 'pre-alert-' . $shipment->shipment_number . '-' . $latestPreAlert->version . '.pdf',
                'content' => (string) file_get_contents(Storage::disk('public')->path($latestPreAlert->file_path)),
                'mime' => 'application/pdf',
            ];
        }

        return array_merge($attachments, $this->buildUploadedDocumentAttachments($shipment, $documentIds));
    }

    /**
     * @return array<int, array{filename: string, content: string, mime: string}>
     */
    private function buildUploadedDocumentAttachments(Shipment $shipment, array $documentIds): array
    {
        if ($documentIds === []) {
            return [];
        }

        $attachments = [];

        foreach ($shipment->documents as $document) {
            if (!in_array($document->id, $documentIds, true)) {
                continue;
            }

            $path = Storage::disk('public')->path($document->file_path);
            if (!is_file($path)) {
                continue;
            }

            $attachments[] = [
                'filename' => $document->file_name,
                'content' => (string) file_get_contents($path),
                'mime' => 'application/pdf',
            ];
        }

        return $attachments;
    }

    private function resolveConsigneeContact(Shipment $shipment, array $partyNames): array
    {
        $composite = $shipment->consignee;
        $result = [
            'name' => '',
            'email' => $shipment->consignee_email ?? '',
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
            case 'agent':
                $agent = \App\Models\Agent::find($id);
                if ($agent) {
                    $result['name'] = $agent->agent_name;
                    $result['email'] = $agent->email ?: $result['email'];
                }
                break;
            case 'hub':
                $hub = \App\Models\Hub::find($id);
                if ($hub) {
                    $result['name'] = $hub->hub_name;
                    $result['email'] = $hub->email ?: $result['email'];
                }
                break;
            case 'office':
                $office = \App\Models\Office::find($id);
                if ($office) {
                    $result['name'] = $office->office_name;
                    $result['email'] = $office->email ?: $result['email'];
                }
                break;
        }

        return $result;
    }
}
