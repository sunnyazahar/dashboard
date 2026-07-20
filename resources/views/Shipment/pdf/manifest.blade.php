<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manifest {{ $shipment->shipment_number }}</title>
    <style>
        @page { size: A4; margin: 12mm 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; line-height: 1.4; margin: 0; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .header-table td { vertical-align: top; }
        .doc-title { font-size: 17px; font-weight: bold; margin: 0 0 4px; }
        .doc-subtitle { font-size: 13px; font-weight: bold; margin: 0 0 8px; }
        .company { font-size: 12px; font-weight: bold; }
        .muted { color: #555; font-size: 11px; }
        .header-right { text-align: right; font-size: 10px; }
        .brand-logo { line-height: 1.05; margin-bottom: 4px; }
        .brand-marine { font-size: 20px; font-weight: bold; color: #002D5B; }
        .brand-caddie { font-size: 20px; font-weight: bold; color: #349DDA; }
        .brand-tagline { display: block; font-size: 9px; color: #FF6B03; font-weight: bold; margin-top: 2px; }
        .section-title { font-size: 13px; font-weight: bold; margin: 12px 0 8px; }
        .field-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 11px; }
        .field-table td { padding: 3px 0; vertical-align: top; }
        .field-label { width: 30%; font-weight: bold; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 10px; }
        .data-table th, .data-table td { border: 0.5px solid #ccc; padding: 5px 4px; text-align: left; }
        .data-table th { background: #f3f4f6; font-weight: bold; }
        .totals-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11px; }
        .totals-table td { padding: 3px 0; }
        .totals-label { width: 38%; font-weight: bold; }
        .footer-ref { margin-top: 14px; font-size: 10px; font-weight: bold; }
        .page-footer {
            margin-top: 18px;
            padding-top: 8px;
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            color: #222;
            line-height: 1.35;
        }
        .page-footer td {
            vertical-align: top;
            padding: 0;
        }
        .page-footer-left { width: 38%; text-align: left; }
        .page-footer-center { width: 24%; text-align: center; vertical-align: middle; }
        .page-footer-right { width: 38%; text-align: right; }
        .comments { white-space: pre-wrap; font-size: 10px; margin-top: 8px; }
        .vessel-heading { font-size: 12px; font-weight: bold; margin: 10px 0 6px; }
        .pending-eta { font-size: 10px; color: #666; margin: 6px 0 2px; }
        .onboard-receipt {
            margin-top: 0;
            width: 100%;
        }
        .onboard-receipt-labels {
            width: 100%;
            border-collapse: collapse;
        }
        .onboard-receipt-labels td {
            padding: 0;
            font-size: 14px;
            font-weight: bold;
            color: #222;
            vertical-align: top;
            width: 33.33%;
        }
        .onboard-receipt-space {
            height: 48px;
        }
        .onboard-receipt-line {
            width: 50%;
            border-top: 1px dashed #9ca3af;
            margin: 0 0 8px;
        }
        .onboard-receipt-signatory {
            font-size: 14px;
            color: #222;
            font-weight: bold;
        }
        .page-manifest-invoice {
            position: relative;
            min-height: 255mm;
        }
        .page-manifest-invoice .onboard-receipt-wrap {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 20;
        }
        .page-manifest-invoice .page-footer {
            margin-top: 10px;
        }
    </style>
</head>
<body>

@php
    $header = function ($docTitle) use ($titleLine, $companyName, $companyAddress) {
        return '
        <table class="header-table">
            <tr>
                <td style="width:62%;">
                    <div class="doc-title">' . e($docTitle) . '</div>
                    <div class="doc-subtitle">' . e($titleLine) . '</div>
                    <div class="company">' . e($companyName) . '</div>
                    <div class="muted">' . e($companyAddress) . '</div>
                </td>
                <td class="header-right" style="width:38%;">
                    <div class="brand-logo">
                        <span class="brand-marine">Marine</span><span class="brand-caddie">Caddie</span>
                        <span class="brand-tagline">Smart Caddies, Smarter Logistics !</span>
                    </div>
                </td>
            </tr>
        </table>';
    };

    $footer = function ($pageNo, $pageTotal) use ($createdAt) {
        return '
        <table class="page-footer">
            <tr>
                <td class="page-footer-left">
                    MarineCaddie India Private Limited<br>
                    Innov8 Aerocity, Asset-5A, Hospitality District<br>
                    Near IGI Airport, Aerocity, New Delhi-110037.
                </td>
                <td class="page-footer-center">' . e($pageNo) . '/' . e($pageTotal) . '</td>
                <td class="page-footer-right">
                    +919560773375 ops@marinecaddie.com<br>
                    Created on ' . e($createdAt) . '
                </td>
            </tr>
        </table>';
    };
@endphp

{{-- Shipping instructions (single page) --}}
<div class="page">
    {!! $header('Shipping instructions') !!}
    <table class="field-table">
        <tr><td class="field-label">Shipped through</td><td>{{ $shippedThrough }}</td></tr>
        <tr><td class="field-label">Invoice to</td><td>{{ $invoiceTo }}</td></tr>
    </table>
    <div class="section-title" style="margin-top:10px;">Please prepare shipment to</div>
    <div style="font-weight:bold;">{{ $vesselLine }}</div>
    <table class="field-table" style="margin-top:6px;">
        <tr><td class="field-label">C/O</td><td>{{ $consigneeName }}</td></tr>
        <tr><td class="field-label"></td><td>{{ $consigneeAddress }}</td></tr>
        <tr><td class="field-label">E-mail</td><td>{{ $consigneeEmail }}</td></tr>
        <tr><td class="field-label">Phone</td><td>{{ $consigneePhone }}</td></tr>
        <tr><td class="field-label">Contact Person</td><td>{{ $consigneeContact ?: '—' }}</td></tr>
        <tr><td class="field-label">Port of departure</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="field-label">Port of destination</td><td>{{ $destinationPort }}</td></tr>
        <tr><td class="field-label">Location</td><td>{{ $shipmentLocation }}</td></tr>
        <tr><td class="field-label">Service</td><td>{{ $serviceLabel }}</td></tr>
        <tr><td class="field-label">Additional service</td><td>{{ $additionalServiceLabel }}</td></tr>
        <tr><td class="field-label">PCS / Repacked as / Weight</td><td>{{ $pcsSummary }}</td></tr>
        <tr><td class="field-label">Deadline arrival</td><td>{{ $deadlineArrival }}</td></tr>
        <tr><td class="field-label">Document handled by</td><td>{{ $documentHandledBy }}</td></tr>
    </table>
    <div class="section-title" style="margin-top:10px;">Comments to hub</div>
    <div class="comments">{{ $commentsHub ?: '—' }}</div>
    {!! $footer('1', '3') !!}
</div>

{{-- Manifest / Invoice --}}
<div class="page page-manifest-invoice">
    {!! $header('Manifest / Invoice') !!}
    <div class="vessel-heading">{{ $vesselLine }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>PO number</th>
                <th>Items</th>
                <th>Weight</th>
                <th>CBM</th>
                <th>Cust. value</th>
                <th>Description</th>
                <th>Stock no / Transit id</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($manifestRows as $row)
            <tr>
                <td>{{ $row['supplier'] }}</td>
                <td>{{ $row['po_number'] }}</td>
                <td>{{ $row['items'] }}</td>
                <td>{{ $row['weight'] }}</td>
                <td>{{ number_format($row['cbm'], 2) }}</td>
                <td>{{ $row['customs_value'] }} {{ $row['currency'] }}</td>
                <td>{{ $row['description'] }}</td>
                <td>{{ $row['stock_number'] }}@if($row['transit_id']) / {{ $row['transit_id'] }}@endif</td>
                <td>{{ $row['location'] ?? '—' }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2"><strong>Total {{ $manifestRows->first()['vessel'] ?? $vesselLine }}</strong></td>
                <td><strong>{{ $totals['packages'] }} pcs</strong></td>
                <td><strong>{{ $totals['weight'] }} kg</strong></td>
                <td><strong>{{ number_format($totals['cbm'], 2) }} CBM</strong></td>
                <td><strong>{{ $totals['customs_value'] }} {{ $totals['currency'] }}</strong></td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>
    <table class="totals-table">
        <tr><td class="totals-label">Total in consignment</td><td>{{ $totals['packages'] }} pcs</td></tr>
        <tr><td class="totals-label">Total weight</td><td>{{ $totals['weight'] }} kg</td></tr>
        <tr><td class="totals-label">Estimated volume weight</td><td>{{ number_format($totals['volume_weight'], 2) }} kg</td></tr>
        <tr><td class="totals-label">Total customs value</td><td>{{ $totals['customs_value'] }} {{ $totals['currency'] }}</td></tr>
        <tr><td class="totals-label">Total CBM</td><td>{{ number_format($totals['cbm'], 2) }} m³</td></tr>
        <tr><td class="totals-label">Total CBFT</td><td>{{ number_format($totals['cbft'], 2) }} ft³</td></tr>
        <tr><td class="totals-label">Port of departure</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="totals-label">Shipper</td><td>{{ $shipperLine }}</td></tr>
        <tr><td class="totals-label">Consignee</td><td>{{ $consigneeLine }}</td></tr>
        <tr><td class="totals-label">Contact</td><td>{{ $consigneeContact }}, {{ $consigneeContactEmail }}, {{ $consigneeContactPhone }}</td></tr>
        <tr><td class="totals-label">Customer</td><td>{{ $customerName }}</td></tr>
    </table>
    <div class="onboard-receipt-wrap">
        @if (($serviceLabel ?? '') === 'On-board delivery')
            <div class="onboard-receipt">
                <table class="onboard-receipt-labels">
                    <tr>
                        <td style="text-align:left;">Date received</td>
                        <td style="text-align:center;">Stamp</td>
                        <td></td>
                    </tr>
                </table>
                <div class="onboard-receipt-space"></div>
                <div class="onboard-receipt-line"></div>
                <div class="onboard-receipt-signatory">{{ $onBoardSignatory }}</div>
            </div>
        @endif
        {!! $footer('2', '3') !!}
    </div>
</div>

{{-- Packing list (single page) --}}
<div class="page">
    {!! $header('Packing list') !!}
    <div class="vessel-heading">{{ $manifestRows->first()['vessel'] ?? $vesselLine }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Stock no</th>
                <th>Location Position</th>
                <th>Supplier</th>
                <th>PO number</th>
                <th>Items</th>
                <th>Weight</th>
                <th>Dimensions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($packingRows as $index => $row)
                @if ($row['pending_eta'] && ($index === 0 || ($packingRows[$index - 1]['stock_number'] ?? null) !== $row['stock_number']))
                <tr>
                    <td colspan="7" class="pending-eta" style="font-weight: bold;">In Transit &nbsp;&nbsp;  Transit ID: &nbsp;&nbsp; {{ $row['transit_id'] ?? '' }} &nbsp;&nbsp; ETA: &nbsp;&nbsp; {{ $row['pending_eta'] }}</td>
                </tr>
                @endif
                <tr>
                    <td>{{ $row['stock_number'] }}<br>{{ $row['label_code'] }}</td>
                    <td>{{ $row['position'] }}</td>
                    <td>{{ $row['supplier'] }}</td>
                    <td>{{ $row['po_number'] }}</td>
                    <td>{{ $row['item_label'] }}</td>
                    <td>{{ $row['weight_label'] }}</td>
                    <td>{{ $row['dimensions'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"><strong>Total</strong></td>
                <td><strong>{{ $totals['packages'] }} pcs</strong></td>
                <td><strong>{{ $totals['weight'] }} kg</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table class="field-table" style="margin-top:8px;">
        <tr><td class="field-label">Shipper</td><td>{{ $shipperLine }}</td></tr>
        <tr><td class="field-label">Consignee</td><td>{{ $consigneeLine }}</td></tr>
        <tr><td class="field-label">Departure</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="field-label">Destination</td><td>{{ $destinationPort }}</td></tr>
        <tr><td class="field-label">Deadline date</td><td>{{ $deadlineArrival }}</td></tr>
    </table>
    <table class="totals-table" style="margin-top:8px;">
        <tr><td class="totals-label">Total in consignment</td><td>{{ $totals['packages'] }} pcs</td></tr>
        <tr><td class="totals-label">Total weight</td><td>{{ $totals['weight'] }} kg</td></tr>
        <tr><td class="totals-label">Estimated volume weight</td><td>{{ number_format($totals['volume_weight'], 2) }} kg</td></tr>
        <tr><td class="totals-label">Total customs value</td><td>{{ $totals['customs_value'] }} {{ $totals['currency'] }}</td></tr>
        <tr><td class="totals-label">Total CBM</td><td>{{ number_format($totals['cbm'], 2) }} m³</td></tr>
        <tr><td class="totals-label">Total CBFT</td><td>{{ number_format($totals['cbft'], 2) }} ft³</td></tr>
    </table>
    {!! $footer('3', '3') !!}
</div>

</body>
</html>
