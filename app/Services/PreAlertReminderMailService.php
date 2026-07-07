<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Hub;
use App\Models\Office;
use App\Models\Shipment;
use RuntimeException;

class PreAlertReminderMailService
{
    public function __construct(
        private ShipmentManifestPdfBuilder $manifestPdfBuilder,
        private ShipmentPreAlertPdfBuilder $preAlertPdfBuilder,
        private EmlMessageBuilder $emlMessageBuilder,
    ) {}

    public function buildEml(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): string
    {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail);

        return $this->emlMessageBuilder->build(
            $mail['senderName'],
            $mail['senderEmail'],
            $mail['to'],
            $mail['cc'],
            $mail['subject'],
            $mail['body'],
            []
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

    private function prepareMail(Shipment $shipment, ?string $senderName, ?string $senderEmail): array
    {
        $shipment->loadMissing([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
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
        $departureParty = $this->resolveDepartureContact($shipment, $partyNames);

        if (empty($departureParty['email'])) {
            throw new RuntimeException('No email address found for the departure party.');
        }

        $senderName = $senderName ?: ($shipment->accountManager?->name ?? $shipment->creator?->name ?? 'Marinetrans');
        $senderEmail = $senderEmail ?: ($shipment->accountManager?->email ?? $shipment->creator?->email ?? config('mail.from.address', 'esea@marinetrans.net'));

        return [
            'senderName' => $senderName,
            'senderEmail' => $senderEmail,
            'subject' => $this->buildSubject($shipment, $manifestData),
            'body' => $this->buildBody($shipment, $manifestData, $departureParty, $senderName, $senderEmail),
            'to' => $this->buildToAddresses($departureParty),
            'cc' => $this->buildCcAddresses($shipment, $senderEmail, $departureParty['email']),
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
            'Manifest for %s / %s / %s / %s / MT REF: %s / From %s to %s',
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
        array $departureParty,
        string $senderName,
        string $senderEmail
    ): string {
        $departureName = $departureParty['name'] ?: 'Sir/Madam';
        $destination = $manifestData['destinationPort'] ?? 'destination';
        $service = $shipment->service ?? 'shipment';
        $deadline = $shipment->deadline_arrival?->format('d.m.Y') ?? '—';

        $lines = [
            'To ' . $departureName,
            '',
            'Please prepare ' . $service . ' to ' . $destination,
            '',
            'Deadline ' . $deadline,
            '',
        ];

        $serviceDetailLines = $this->preAlertPdfBuilder->reminderMailServiceDetailLines($shipment);
        if ($serviceDetailLines !== []) {
            array_push($lines, ...$serviceDetailLines);
            $lines[] = '';
        }

        if ($shipment->customer_reference) {
            $lines[] = 'As per quote ' . $shipment->customer_reference . ', pls check with agent for boat loading care off their boat';
            $lines[] = '';
        }

        if ($shipment->comments_departure_hub) {
            $lines[] = $shipment->comments_departure_hub;
            $lines[] = '';
        }

        $lines[] = 'Pls keep us posted.';
        $lines[] = '';
        $lines[] = '';
        $lines[] = 'With kind regards,';
        $lines[] = '';
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

        return implode("\r\n", $lines);
    }

    /**
     * @return array<int, array{name?: string, email: string}>
     */
    private function buildToAddresses(array $departureParty): array
    {
        if (empty($departureParty['email'])) {
            return [];
        }

        return [[
            'name' => $departureParty['name'] ?? '',
            'email' => $departureParty['email'],
        ]];
    }

    /**
     * @return array<int, array{name?: string, email: string}>
     */
    private function buildCcAddresses(Shipment $shipment, string $senderEmail, string $departureEmail): array
    {
        $addresses = [];

        if ($shipment->accountManager?->email
            && $shipment->accountManager->email !== $senderEmail
            && $shipment->accountManager->email !== $departureEmail) {
            $addresses[] = [
                'name' => $shipment->accountManager->name,
                'email' => $shipment->accountManager->email,
            ];
        }

        return $addresses;
    }

    private function resolveDepartureContact(Shipment $shipment, array $partyNames): array
    {
        $composite = $shipment->departure;
        $result = [
            'name' => '',
            'email' => '',
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
                $agent = Agent::find($id);
                if ($agent) {
                    $result['name'] = $agent->agent_name;
                    $result['email'] = $agent->email ?? '';
                }
                break;
            case 'hub':
                $hub = Hub::find($id);
                if ($hub) {
                    $result['name'] = $hub->hub_name;
                    $result['email'] = $hub->email ?? '';
                }
                break;
            case 'office':
                $office = Office::find($id);
                if ($office) {
                    $result['name'] = $office->office_name;
                    $result['email'] = $office->email ?? '';
                }
                break;
        }

        return $result;
    }
}
