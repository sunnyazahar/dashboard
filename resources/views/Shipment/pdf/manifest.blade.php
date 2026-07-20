<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manifest {{ $shipment->shipment_number }}</title>
    <style>
        @page { size: A4; margin: 12mm 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #222; line-height: 1.35; margin: 0; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .header-table td { vertical-align: top; }
        .doc-title { font-size: 14px; font-weight: bold; margin: 0 0 4px; }
        .doc-subtitle { font-size: 11px; font-weight: bold; margin: 0 0 8px; }
        .company { font-size: 10px; font-weight: bold; }
        .muted { color: #555; }
        .header-right { text-align: right; font-size: 8px; }
        .section-title { font-size: 11px; font-weight: bold; margin: 14px 0 8px; }
        .field-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .field-table td { padding: 2px 0; vertical-align: top; }
        .field-label { width: 28%; font-weight: bold; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 8px; }
        .data-table th, .data-table td { border: 0.5px solid #ccc; padding: 4px 3px; text-align: left; }
        .data-table th { background: #f3f4f6; font-weight: bold; }
        .totals-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8px; }
        .totals-table td { padding: 2px 0; }
        .totals-label { width: 35%; font-weight: bold; }
        .footer-ref { margin-top: 16px; font-size: 8px; font-weight: bold; }
        .comments { white-space: pre-wrap; font-size: 8px; margin-top: 8px; }
        .vessel-heading { font-size: 10px; font-weight: bold; margin: 10px 0 6px; }
        .pending-eta { font-size: 8px; color: #666; margin: 6px 0 2px; }
    </style>
</head>
<body>

@php
    $header = function ($docTitle, $pageNo, $pageTotal) use ($titleLine, $companyName, $companyAddress, $companyPhone, $companyEmail, $createdAt) {
        return '
        <table class="header-table">
            <tr>
                <td style="width:70%;">
                    <div class="doc-title">' . e($docTitle) . '</div>
                    <div class="doc-subtitle">' . e($titleLine) . '</div>
                    <div class="company">' . e($companyName) . '</div>
                    <div class="muted">' . e($companyAddress) . '</div>
                </td>
                <td class="header-right" style="width:30%;">
                    <div>' . e($pageNo) . ' / ' . e($pageTotal) . '</div>
                    <div>' . e($companyPhone) . ' ' . e($companyEmail) . '</div>
                    <div>Created on ' . e($createdAt) . '</div>
                </td>
            </tr>
        </table>';
    };
@endphp

{{-- Page 1: Shipping instructions --}}
<div class="page">
    {!! $header('Shipping instructions', '1', '2') !!}
    <table class="field-table">
        <tr><td class="field-label">Shipped through</td><td>{{ $shippedThrough }}</td></tr>
        <tr><td class="field-label">Invoice to</td><td>{{ $invoiceTo }}</td></tr>
    </table>
    <div class="section-title">Please prepare shipment to</div>
    <div style="font-weight:bold;">{{ $vesselLine }}</div>
    <table class="field-table" style="margin-top:8px;">
        <tr><td class="field-label">C/O</td><td>{{ $consigneeName }}</td></tr>
        <tr><td class="field-label"></td><td>{{ $consigneeAddress }}</td></tr>
        <tr><td class="field-label">E-mail</td><td>{{ $consigneeEmail }}</td></tr>
        <tr><td class="field-label">Phone</td><td>{{ $consigneeContactPhone }}</td></tr>
        <tr><td class="field-label">Contact</td><td>{{ $consigneeContact }}</td></tr>
        <tr><td class="field-label">Contact email</td><td>{{ $consigneeContactEmail }}</td></tr>
        <tr><td class="field-label">Contact phone</td><td>{{ $consigneeContactPhone }}</td></tr>
        <tr><td class="field-label">Agent</td><td>{{ $agentName }}</td></tr>
        <tr><td class="field-label">Port of departure</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="field-label">Port of destination</td><td>{{ $destinationPort }}</td></tr>
        <tr><td class="field-label">Document handled by</td><td>{{ $documentHandledBy }}</td></tr>
        <tr><td class="field-label">Service</td><td>{{ $serviceLabel }}</td></tr>
        <tr><td class="field-label">Additional service</td><td>{{ $additionalServiceLabel }}</td></tr>
        <tr><td class="field-label">PCS / Repacked as / Weight</td><td>{{ $pcsSummary }}</td></tr>
        <tr><td class="field-label">Deadline arrival</td><td>{{ $deadlineArrival }}</td></tr>
    </table>
    <div class="footer-ref">Combined PO document link<br>{{ $combinedPoReference }}</div>
</div>

{{-- Page 2: Shipping instructions (comments) --}}
<div class="page">
    {!! $header('Shipping instructions', '2', '2') !!}
    <div class="section-title">Comments to hub</div>
    <div class="comments">{{ $commentsHub ?: '—' }}</div>
    <div class="footer-ref">Combined PO document link<br>{{ $combinedPoReference }}</div>
</div>

{{-- Page 3: Manifest / Invoice --}}
<div class="page">
    {!! $header('Manifest / Invoice', '1', '1') !!}
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
    <div class="footer-ref">Combined PO document link<br>{{ $combinedPoReference }}</div>
</div>

{{-- Page 4-5: Packing list --}}
<div class="page">
    {!! $header('Packing list', '1', '2') !!}
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
                    <td colspan="7" class="pending-eta">Pending ETA: {{ $row['pending_eta'] }}</td>
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
    <table class="field-table" style="margin-top:10px;">
        <tr><td class="field-label">Shipper</td><td>{{ $shipperLine }}</td></tr>
        <tr><td class="field-label">Consignee</td><td>{{ $consigneeLine }}</td></tr>
        <tr><td class="field-label">Departure</td><td>{{ $departurePort }}</td></tr>
        <tr><td class="field-label">Destination</td><td>{{ $destinationPort }}</td></tr>
        <tr><td class="field-label">Deadline date</td><td>{{ $deadlineArrival }}</td></tr>
    </table>
    <div class="footer-ref">Combined PO document link {{ $combinedPoReference }}</div>
</div>

<div class="page">
    {!! $header('Packing list', '2', '2') !!}
    <table class="totals-table">
        <tr><td class="totals-label">Total in consignment</td><td>{{ $totals['packages'] }} pcs</td></tr>
        <tr><td class="totals-label">Total weight</td><td>{{ $totals['weight'] }} kg</td></tr>
        <tr><td class="totals-label">Estimated volume weight</td><td>{{ number_format($totals['volume_weight'], 2) }} kg</td></tr>
        <tr><td class="totals-label">Total customs value</td><td>{{ $totals['customs_value'] }} {{ $totals['currency'] }}</td></tr>
        <tr><td class="totals-label">Total CBM</td><td>{{ number_format($totals['cbm'], 2) }} m³</td></tr>
        <tr><td class="totals-label">Total CBFT</td><td>{{ number_format($totals['cbft'], 2) }} ft³</td></tr>
    </table>
</div>

</body>
</html>
