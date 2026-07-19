<?php

namespace App\Services;

use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ManifestMailService
{
    public function __construct(
        private ShipmentManifestPdfBuilder $manifestPdfBuilder,
        private ShipmentPreAlertPdfBuilder $preAlertPdfBuilder,
        private CombinedPoPdfService $combinedPoPdfService,
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
            'crrs.documents',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
            'documents',
            'manifests',
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
            'body' => $this->buildBody($shipment, $consigneeParty, $senderName, $senderEmail),
            'to' => $this->buildToAddresses($consigneeParty),
            'cc' => $this->buildCcAddresses($shipment, $senderEmail, $consigneeParty),
            'attachments' => $this->buildAttachments($shipment, $manifestData, $documentIds),
        ];
    }

    private function buildSubject(Shipment $shipment, array $manifestData): string
    {
        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $service = $shipment->service ?? '—';
        $departure = $manifestData['departurePort'] ?? ($shipment->departure_port_code ?: '—');
        $destination = collect([
            $shipment->consignee_port_code,
            $shipment->consignee_city,
        ])->filter()->implode(' - ');

        if ($destination === '') {
            $destination = $manifestData['destinationPort'] ?? '—';
        }

        return sprintf(
            'Manifest for Shipment Ref. %s / %s / %s / From %s / to %s',
            $shipment->shipment_number,
            $vessel,
            $service,
            $departure,
            $destination
        );
    }

    private function buildBody(
        Shipment $shipment,
        array $consigneeParty,
        string $senderName,
        string $senderEmail
    ): string {
        $consigneeName = $consigneeParty['name'] ?: ($shipment->consignee_att ?: 'Sir/Madam');
        $destination = $this->buildDestinationLabel($shipment);
        $service = $shipment->service ?? 'shipment';
        $deadline = $shipment->deadline_arrival?->format('d.m.Y') ?? '—';

        $lines = [
            'To ' . $consigneeName,
            '',
            'Please prepare ' . $service . ' to ' . $destination . ' and provide service details.',
            '',
            '',
            'Service Details:',
            '',
        ];

        array_push($lines, ...$this->preAlertPdfBuilder->reminderMailServiceDetailLines($shipment));

        array_push($lines,
            '',
            'Deadline ' . $deadline,
            '',
            '',
            '',
            'With kind regards,',
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
        ])->filter()->implode(' / ') ?: 'destination';
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
    private function buildCcAddresses(Shipment $shipment, string $senderEmail, array $consigneeParty): array
    {
        $addresses = [];
        $toEmail = $consigneeParty['email'] ?? '';
        $seen = [];

        $addAddress = function (?string $name, ?string $email) use (&$addresses, &$seen): void {
            $email = trim((string) $email);
            if ($email === '' || isset($seen[$email])) {
                return;
            }

            $seen[$email] = true;
            $addresses[] = [
                'name' => $name ?? '',
                'email' => $email,
            ];
        };

        $addAddress('', $senderEmail);

        if ($shipment->accountManager?->email) {
            $addAddress($shipment->accountManager->name, $shipment->accountManager->email);
        }

        $addAddress($consigneeParty['name'] ?? $shipment->consignee_att, $toEmail);
        $addAddress($shipment->consignee_att, $shipment->consignee_email);

        return $addresses;
    }

    /**
     * @return array<int, array{filename: string, content: string, mime: string}>
     */
    private function buildAttachments(Shipment $shipment, array $manifestData, array $documentIds = []): array
    {
        $attachments = [];

        $latestManifest = $shipment->manifests->sortByDesc('version')->first();
        if ($latestManifest && is_file(Storage::disk('public')->path($latestManifest->file_path))) {
            $attachments[] = [
                'filename' => $latestManifest->file_name . '-' . $shipment->shipment_number . '.pdf',
                'content' => (string) file_get_contents(Storage::disk('public')->path($latestManifest->file_path)),
                'mime' => 'application/pdf',
            ];
        } else {
            $manifestPdf = Pdf::loadView('Shipment.pdf.manifest', $manifestData)
                ->setPaper('a4', 'portrait')
                ->output();

            $attachments[] = [
                'filename' => 'manifest-' . $shipment->shipment_number . '.pdf',
                'content' => $manifestPdf,
                'mime' => 'application/pdf',
            ];
        }

        $combinedPoAttachment = $this->combinedPoPdfService->buildAttachmentForShipment($shipment);
        if ($combinedPoAttachment !== null) {
            $attachments[] = $combinedPoAttachment;
        } else {
            foreach ($this->combinedPoPdfService->individualAttachmentsForShipment($shipment) as $poAttachment) {
                $attachments[] = $poAttachment;
            }
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
