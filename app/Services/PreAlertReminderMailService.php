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

        return $this->previewFromMail($mail);
    }

    public function buildDeliveryStatusEml(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): string
    {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail, 'delivery_status');

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

    public function buildDeliveryStatusPreview(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): array
    {
        return $this->previewFromMail(
            $this->prepareMail($shipment, $senderName, $senderEmail, 'delivery_status')
        );
    }

    public function buildInvoiceRequestEml(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): string
    {
        $mail = $this->prepareMail($shipment, $senderName, $senderEmail, 'invoice_request');

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

    public function buildInvoiceRequestPreview(Shipment $shipment, ?string $senderName = null, ?string $senderEmail = null): array
    {
        return $this->previewFromMail(
            $this->prepareMail($shipment, $senderName, $senderEmail, 'invoice_request')
        );
    }

    private function previewFromMail(array $mail): array
    {
        return [
            'to' => collect($mail['to'])->pluck('email')->filter()->implode(','),
            'cc' => collect($mail['cc'])->pluck('email')->filter()->implode(','),
            'subject' => $mail['subject'],
            'body' => preg_replace("/\r\n|\r|\n/", "\n", $mail['body']),
        ];
    }

    private function prepareMail(
        Shipment $shipment,
        ?string $senderName,
        ?string $senderEmail,
        string $mailType = 'pre_alert'
    ): array
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
            'onBoardLegs',
        ]);

        if ($shipment->crrs->isEmpty()) {
            throw new RuntimeException('No stock items linked to this shipment.');
        }

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
            'subject' => match ($mailType) {
                'delivery_status' => $this->buildDeliveryStatusSubject($shipment),
                'invoice_request' => $this->buildInvoiceRequestSubject($shipment),
                default => $this->buildSubject($shipment),
            },
            'body' => match ($mailType) {
                'delivery_status' => $this->buildDeliveryStatusBody($shipment, $departureParty, $senderName, $senderEmail),
                'invoice_request' => $this->buildInvoiceRequestBody($shipment, $departureParty, $senderName, $senderEmail),
                default => $this->buildBody($shipment, $departureParty, $senderName, $senderEmail),
            },
            'to' => $this->buildToAddresses($departureParty),
            'cc' => $this->buildCcAddresses($shipment, $senderEmail, $departureParty['email']),
        ];
    }

    private function buildSubject(Shipment $shipment): string
    {
        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $service = $shipment->service ?? '—';

        return sprintf(
            'Reminder: Outgoing shipment details %s/ %s/ MT REF: %s / %s',
            $vessel,
            $service,
            $shipment->shipment_number,
            $this->buildDestinationLabel($shipment)
        );
    }

    private function buildBody(
        Shipment $shipment,
        array $departureParty,
        string $senderName,
        string $senderEmail
    ): string {
        $departureName = $departureParty['name'] ?: 'Sir/Madam';
        $service = $shipment->service ?? 'shipment';

        $lines = [
            'To ' . $departureName,
            '',
            'Please provide the details of ' . $service . ' to ' . $this->buildDestinationLabel($shipment),
            '',
            '',
        ];

        array_push($lines, ...$this->preAlertPdfBuilder->reminderMailServiceDetailLines($shipment));

        array_push($lines,
            '',
            '',
            'With kind regards,',
            '',
            $senderName,
            $senderEmail,
            'Marincaddie',
        );

        return implode("\r\n", $lines);
    }

    private function buildDeliveryStatusSubject(Shipment $shipment): string
    {
        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $service = $shipment->service ?? '—';

        return sprintf(
            'Delivery status request - %s/ %s/ MT REF: %s / %s',
            $vessel,
            $service,
            $shipment->shipment_number,
            $this->buildDeliveryDestinationLabel($shipment)
        );
    }

    private function buildDeliveryStatusBody(
        Shipment $shipment,
        array $departureParty,
        string $senderName,
        string $senderEmail
    ): string {
        $service = $shipment->service ?? 'shipment';
        $lines = [
            'To ' . ($departureParty['name'] ?: 'Sir/Madam'),
            '',
            'Please provide the delivery status of ' . $service . ' to ' . $this->buildDeliveryDestinationLabel($shipment),
            '',
            '',
        ];

        array_push($lines, ...$this->preAlertPdfBuilder->reminderMailServiceDetailLines($shipment));

        array_push($lines,
            '',
            '',
            'With kind regards,',
            '',
            $senderName,
            $senderEmail,
            'Marincaddie',
        );

        return implode("\r\n", $lines);
    }

    private function buildInvoiceRequestSubject(Shipment $shipment): string
    {
        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $departurePort = $shipment->departure_port_code ?: '—';
        $destinationPort = $shipment->consignee_port_code ?: '—';

        return sprintf(
            'Invoice Request - %s/%s/%s / %s',
            $shipment->shipment_number,
            $vessel,
            $departurePort,
            $destinationPort
        );
    }

    private function buildInvoiceRequestBody(
        Shipment $shipment,
        array $departureParty,
        string $senderName,
        string $senderEmail
    ): string {
        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $lines = [
            'Attn: ' . ($departureParty['name'] ?: 'Sir/Madam'),
            '',
            sprintf(
                'Please provide your Debit Note (Billing Invoice) for shipment Ref. %s / %s.',
                $shipment->shipment_number,
                $vessel
            ),
            '',
            'Shipment Details:',
            '',
        ];

        array_push($lines, ...$this->preAlertPdfBuilder->reminderMailServiceDetailLines($shipment));

        array_push($lines,
            '',
            '',
            'With kind regards,',
            '',
            $senderName,
            $senderEmail,
            'Marincaddie',
        );

        return implode("\r\n", $lines);
    }

    private function buildDestinationLabel(Shipment $shipment): string
    {
        $portAndCity = collect([
            $shipment->consignee_port_code,
            $shipment->consignee_city,
        ])->filter()->implode(' - ');

        return collect([
            $portAndCity,
            $shipment->location,
        ])->filter()->implode(' / ') ?: '—';
    }

    private function buildDeliveryDestinationLabel(Shipment $shipment): string
    {
        $portCityDistrict = collect([
            $shipment->consignee_port_code,
            $shipment->consignee_city,
            $shipment->consignee_district,
        ])->filter()->implode(' - ');

        return collect([
            $portCityDistrict,
            $shipment->location,
        ])->filter()->implode(' / ') ?: '—';
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
