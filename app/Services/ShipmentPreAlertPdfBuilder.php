<?php

namespace App\Services;

use App\Models\Crr;
use App\Models\Shipment;
use App\Models\ShipmentCourierLeg;
use App\Models\ShipmentFlight;
use App\Models\ShipmentHandCarryLeg;
use App\Models\ShipmentOnBoardLeg;
use App\Models\ShipmentReleaseLeg;
use App\Models\ShipmentSeaLeg;
use App\Models\ShipmentTruckLeg;

class ShipmentPreAlertPdfBuilder
{
    public function __construct(
        private ShipmentManifestPdfBuilder $manifestPdfBuilder,
        private CombinedPoPdfService $combinedPoPdfService,
    ) {}

    public function build(Shipment $shipment): array
    {
        $shipment->loadMissing([
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office.country',
            'creator',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ]);

        $base = $this->manifestPdfBuilder->build($shipment);
        $serviceDetails = $this->buildServiceDetails($shipment);

        $primaryVessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $awb = $serviceDetails['awb'] ?? '—';
        $flightNumber = $serviceDetails['flight_number'] ?? '—';
        $arrivalDate = $serviceDetails['arrival_date'] ?? ($shipment->deadline_arrival?->format('d.m.Y') ?? '—');
        $arrivalTime = $serviceDetails['arrival_time'] ?? '—';
        $arrivalLabel = trim($arrivalDate . ($arrivalTime && $arrivalTime !== '—' ? ' ' . $arrivalTime : ''));

        $departurePort = $this->simplePortLabel(
            $shipment->departure_port_code,
            null,
            $base['departurePort'] ?? null
        );
        $destinationPort = $this->simplePortLabel(
            $shipment->consignee_port_code,
            $shipment->consignee_city,
            $base['destinationPort'] ?? null
        );

        $serviceLabel = trim(($shipment->service ?? '—') . ($serviceDetails['service_suffix'] ?? ''));

        $ownerName = $shipment->crrs
            ->map(fn (Crr $crr) => $crr->customerVessel?->customer?->customer_name)
            ->filter()
            ->unique()
            ->implode(', ') ?: '—';

        $accountManager = $shipment->accountManager;
        $accountHandledBy = $accountManager
            ? trim($accountManager->name . ($accountManager->email ? ', ' . $accountManager->email : ''))
            : trim(($shipment->creator?->name ?? '—') . ($shipment->creator?->email ? ', ' . $shipment->creator->email : ''));

        $office = $accountManager?->office;
        $issuedByName = $office?->office_name ?? $base['companyName'] ?? 'Marinetrans';
        $issuedByAddress = $this->formatOfficeAddress($office) ?: ($base['companyAddress'] ?? '—');

        $preAlertRows = $shipment->crrs->map(function (Crr $crr) {
            $poNumbers = is_array($crr->po_numbers)
                ? implode(', ', $crr->po_numbers)
                : ($crr->po_numbers ?? '—');

            $description = $crr->content ?? 'Shipspares';
            if ($crr->hs_code) {
                $description .= "\nHsCode: " . $crr->hs_code;
            }

            return [
                'supplier' => $crr->supplier ?? '—',
                'po_number' => $poNumbers ?: '—',
                'items' => $crr->packages->count(),
                'weight' => round((float) $crr->packages->sum('weight'), 2),
                'cbm' => round((float) $crr->packages->sum('cbm'), 2),
                'customs_value' => $crr->customs_value
                    ? number_format((float) $crr->customs_value, 2) . ' ' . ($crr->currency ?? 'USD')
                    : '—',
                'description' => $description,
                'stock_number' => $crr->stock_number ?? '—',
            ];
        });

        $totals = $base['totals'] ?? [];
        $totalPackages = (int) ($totals['packages'] ?? 0);
        $totalWeight = $totals['weight'] ?? 0;

        return array_merge($base, [
            'headerSubtitle' => $serviceLabel,
            'expectedLine' => sprintf(
                'Shipment is expected on %s in %s with the below details',
                $arrivalDate,
                $destinationPort
            ),
            'departurePortSimple' => $departurePort,
            'destinationPortSimple' => $destinationPort,
            'shippersReference' => $shipment->shipment_number,
            'ownersReference' => $shipment->customer_reference ?? '—',
            'awb' => $awb,
            'flightNumber' => $flightNumber,
            'serviceDeparturePort' => $serviceDetails['departure_port'] ?? $departurePort,
            'arrivalDate' => $arrivalDate,
            'arrivalTime' => $arrivalTime,
            'arrivalLabel' => $arrivalLabel,
            'accountHandledBy' => $accountHandledBy,
            'issuedByName' => $issuedByName,
            'issuedByAddress' => $issuedByAddress,
            'primaryVessel' => $primaryVessel,
            'vesselLine' => $base['vesselLine'] ?? $primaryVessel,
            'serviceLabel' => $serviceLabel,
            'customerReference' => $shipment->customer_reference ?? '—',
            'customerName' => $ownerName,
            'consigneeName' => $base['consigneeName'] ?? '—',
            'consigneeAddressBlock' => $this->formatShippedToBlock($shipment, $base),
            'combinedPoUrl' => $this->combinedPoPdfService->documentsForShipment($shipment)->isNotEmpty()
                ? route('shipments.combined-po-documents', $shipment->id)
                : null,
            'preAlertRows' => $preAlertRows,
            'totalPiecesLabel' => $totalPackages . ' pcs',
            'packedAsLabel' => $totalPackages . ' item(s) / ' . $totalWeight . ' kg',
            'customsValueLabel' => ($totals['customs_value'] ?? '0.00') . ' ' . ($totals['currency'] ?? 'USD'),
            'totalSummaryLabel' => sprintf(
                '%s pcs %s kg %s CBM %s %s',
                $totalPackages,
                $totalWeight,
                number_format((float) ($totals['cbm'] ?? 0), 2),
                $totals['customs_value'] ?? '0.00',
                $totals['currency'] ?? 'USD'
            ),
        ]);
    }

    /**
     * @return array{awb?: string, flight_number?: string, arrival?: string, arrival_date?: string, arrival_time?: string, departure_port?: string, service_suffix?: string, rows: array<int, array{label: string, value: string}>}
     */
    private function buildServiceDetails(Shipment $shipment): array
    {
        $result = ['rows' => []];
        $departurePort = $this->simplePortLabel($shipment->departure_port_code, null, null);

        switch ($shipment->service) {
            case 'Airfreight':
                /** @var ShipmentFlight|null $flight */
                $flight = $shipment->flights->first();
                if (!$flight) {
                    break;
                }

                $result['awb'] = $flight->leg_reference ?: '—';
                $result['flight_number'] = $flight->flight_number ?: '—';
                $result['arrival_date'] = $this->formatDate($flight->arrival_date) ?? '—';
                $result['arrival_time'] = $flight->arrival_time ?: '—';
                $result['arrival'] = $this->formatArrival($flight->arrival_date, $flight->arrival_time);
                $result['departure_port'] = $departurePort;
                $result['service_suffix'] = ', Normal';
                break;

            case 'Sea freight':
                /** @var ShipmentSeaLeg|null $leg */
                $leg = $shipment->seaLegs->first();
                if (!$leg) {
                    break;
                }

                $result['awb'] = $leg->bill_of_lading ?: '—';
                $result['arrival_date'] = $this->formatDate($leg->eta) ?? '—';
                $result['arrival_time'] = $leg->arrival_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->eta, $leg->arrival_time);
                $result['departure_port'] = $departurePort;
                $result['flight_number'] = $leg->transport_vessel_name ?: '—';
                break;

            case 'Truck':
                /** @var ShipmentTruckLeg|null $leg */
                $leg = $shipment->truckLegs->first();
                if (!$leg) {
                    break;
                }

                $result['awb'] = $leg->cmr ?: '—';
                $result['arrival_date'] = $this->formatDate($leg->arrival_date) ?? '—';
                $result['arrival_time'] = $leg->arrival_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->arrival_date, $leg->arrival_time);
                $result['departure_port'] = $departurePort;
                $result['flight_number'] = $leg->freight_company ?: '—';
                break;

            case 'Courier':
                /** @var ShipmentCourierLeg|null $leg */
                $leg = $shipment->courierLegs->first();
                if (!$leg) {
                    break;
                }

                $result['awb'] = $leg->airway_bill ?: '—';
                $result['arrival_date'] = $this->formatDate($leg->arrival_date) ?? '—';
                $result['arrival_time'] = $leg->arrival_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->arrival_date, $leg->arrival_time);
                $result['departure_port'] = $departurePort;
                $result['flight_number'] = $leg->carrier ?: '—';
                break;

            case 'Release':
                /** @var ShipmentReleaseLeg|null $leg */
                $leg = $shipment->releaseLegs->first();
                if (!$leg) {
                    break;
                }

                $result['arrival_date'] = $this->formatDate($leg->delivery_date) ?? '—';
                $result['arrival_time'] = $leg->delivery_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->delivery_date, $leg->delivery_time);
                $result['departure_port'] = $departurePort;
                $result['flight_number'] = $leg->freight_company ?: '—';
                break;

            case 'Hand Carry':
                /** @var ShipmentHandCarryLeg|null $leg */
                $leg = $shipment->handCarryLegs->first();
                if (!$leg) {
                    break;
                }

                $result['arrival_date'] = $this->formatDate($leg->arrival_date) ?? '—';
                $result['arrival_time'] = $leg->arrival_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->arrival_date, $leg->arrival_time);
                $result['departure_port'] = $departurePort;
                $result['flight_number'] = $leg->contact_name ?: '—';
                break;

            case 'On-board delivery':
                /** @var ShipmentOnBoardLeg|null $leg */
                $leg = $shipment->onBoardLegs->first();
                if (!$leg) {
                    break;
                }

                $result['arrival_date'] = $this->formatDate($leg->delivery_date) ?? '—';
                $result['arrival_time'] = $leg->delivery_time ?: '—';
                $result['arrival'] = $this->formatArrival($leg->delivery_date, $leg->delivery_time);
                $result['departure_port'] = $departurePort;
                break;
        }

        return $result;
    }

    /**
     * @return array<int, string>
     */
    public function reminderMailServiceDetailLines(Shipment $shipment): array
    {
        $shipment->loadMissing([
            'crrs',
            'flights',
            'seaLegs',
            'truckLegs',
            'courierLegs',
            'releaseLegs',
            'handCarryLegs',
            'onBoardLegs',
        ]);

        $serviceDetails = $this->buildServiceDetails($shipment);

        $vessel = $shipment->crrs->pluck('vessel_name')->filter()->first() ?? '—';
        $serviceLabel = trim(($shipment->service ?? '—') . ($serviceDetails['service_suffix'] ?? ''));

        $lines = [
            'Vessel: ' . $vessel,
            'Service: ' . $serviceLabel,
        ];

        $detailLines = match ($shipment->service) {
            'Airfreight' => $this->airfreightReminderLines($shipment),
            'Sea freight' => $this->seaFreightReminderLines($shipment),
            'Truck' => $this->truckReminderLines($shipment),
            'Courier' => $this->courierReminderLines($shipment),
            'Release' => $this->releaseReminderLines($shipment),
            'Hand Carry' => $this->handCarryReminderLines($shipment),
            'On-board delivery' => $this->onBoardReminderLines($shipment),
            default => [],
        };

        return array_merge($lines, $detailLines);
    }

    /**
     * @return array<int, string>
     */
    private function airfreightReminderLines(Shipment $shipment): array
    {
        $flight = $shipment->flights->first();

        return [
            'Airway bill: ' . $this->displayValue($flight?->leg_reference),
            'Flight number: ' . $this->displayValue($flight?->flight_number),
            'Departure date: ' . $this->displayValue($flight ? $this->formatDate($flight->departure_date) : null),
            'Arrival date: ' . $this->displayArrivalDate($flight?->arrival_date, $flight?->arrival_time),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function seaFreightReminderLines(Shipment $shipment): array
    {
        $leg = $shipment->seaLegs->first();

        return [
            'B/L: ' . $this->displayValue($leg?->bill_of_lading),
            'Vessel name: ' . $this->displayValue($leg?->transport_vessel_name),
            'Departure date: ' . $this->displayValue($leg ? $this->formatDate($leg->etd) : null),
            'Arrival date: ' . $this->displayArrivalDate($leg?->eta, $leg?->arrival_time),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function truckReminderLines(Shipment $shipment): array
    {
        $leg = $shipment->truckLegs->first();

        return [
            'CMR: ' . $this->displayValue($leg?->cmr),
            'Freight company: ' . $this->displayValue($leg?->freight_company),
            'Departure date: ' . $this->displayValue($leg ? $this->formatDate($leg->departure_date) : null),
            'Arrival date: ' . $this->displayArrivalDate($leg?->arrival_date, $leg?->arrival_time),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function courierReminderLines(Shipment $shipment): array
    {
        $leg = $shipment->courierLegs->first();

        return [
            'Airway bill: ' . $this->displayValue($leg?->airway_bill),
            'Carrier: ' . $this->displayValue($leg?->carrier),
            'Departure date: ' . $this->displayValue($leg ? $this->formatDate($leg->departure_date) : null),
            'Arrival date: ' . $this->displayArrivalDate($leg?->arrival_date, $leg?->arrival_time),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function releaseReminderLines(Shipment $shipment): array
    {
        $leg = $shipment->releaseLegs->first();

        return [
            'Freight company: ' . $this->displayValue($leg?->freight_company),
            'Departure date: ' . '',
            'Arrival date: ' . $this->displayArrivalDate($leg?->delivery_date, $leg?->delivery_time),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function handCarryReminderLines(Shipment $shipment): array
    {
        $leg = $shipment->handCarryLegs->first();

        return [
            'Contact: ' . $this->displayValue($leg?->contact_name),
            'Departure date: ' . $this->displayValue($leg ? $this->formatDate($leg->departure_date) : null),
            'Arrival date: ' . $this->displayArrivalDate($leg?->arrival_date, $leg?->arrival_time),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function onBoardReminderLines(Shipment $shipment): array
    {
        /** @var ShipmentOnBoardLeg|null $leg */
        $leg = $shipment->onBoardLegs->first();

        return [
            'Departure date: ' . $this->displayValue($leg ? $this->formatDate($leg->departure_date) : null),
            'Delivery date: ' . $this->displayValue($leg ? $this->formatDate($leg->delivery_date) : null),
            'Delivery time: ' . $this->displayValue($leg?->delivery_time),
        ];
    }

    private function displayValue(mixed $value): string
    {
        if ($value === null || $value === '' || $value === '—') {
            return '';
        }

        return (string) $value;
    }

    private function displayArrivalDate(mixed $date, ?string $time): string
    {
        $dateLabel = $date ? ($this->formatDate($date) ?? '') : '';

        if ($time) {
            return trim($dateLabel . ' ' . $time);
        }

        return $dateLabel;
    }

    private function simplePortLabel(?string $portCode, ?string $city, ?string $fullLabel): string
    {
        if ($portCode) {
            return $portCode;
        }

        if ($city) {
            return $city;
        }

        if ($fullLabel) {
            return trim(explode('/', $fullLabel)[0]);
        }

        return '—';
    }

    private function formatOfficeAddress($office): string
    {
        if (!$office) {
            return '';
        }

        $lines = array_filter([
            $office->address,
            trim(implode(', ', array_filter([
                $office->zip_code,
                $office->city,
            ]))),
            $office->country?->name ?? null,
        ]);

        return implode("\n", $lines);
    }

    private function formatArrival(mixed $date, ?string $time): ?string
    {
        $dateLabel = $this->formatDate($date);
        if (!$dateLabel) {
            return null;
        }

        return trim($dateLabel . ($time ? ' ' . $time : ''));
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
     * @param  array<string, mixed>  $base
     */
    private function formatShippedToBlock(Shipment $shipment, array $base): string
    {
        $lines = array_filter([
            $base['consigneeName'] ?? null,
            $shipment->consignee_address,
            trim(implode(', ', array_filter([
                $shipment->consignee_zip,
                $shipment->consignee_city,
            ]))),
            $shipment->consignee_country,
        ]);

        return implode("\n", $lines) ?: '—';
    }

    public static function shipmentHasServiceDetails(Shipment $shipment): bool
    {
        return match ($shipment->service) {
            'Airfreight' => $shipment->flights->isNotEmpty(),
            'Sea freight' => $shipment->seaLegs->isNotEmpty(),
            'Truck' => $shipment->truckLegs->isNotEmpty(),
            'Courier' => $shipment->courierLegs->isNotEmpty(),
            'Release' => $shipment->releaseLegs->isNotEmpty(),
            'Hand Carry' => $shipment->handCarryLegs->isNotEmpty(),
            'On-board delivery' => true,
            default => false,
        };
    }
}

