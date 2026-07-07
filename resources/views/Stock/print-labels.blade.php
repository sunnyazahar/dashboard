<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Label - {{ $crr->stock_number }}</title>
    <style>
        @page {
            size: 150mm 100mm;
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 5mm 8mm;
            color: #000;
        }

        .label-page {
            page-break-after: always;
            position: relative;
            height: auto;
        }

        .label-page:last-child {
            page-break-after: avoid;
        }

        .header {
            width: 100%;
            margin-bottom: 5mm;
        }

        .header-left {
            float: left;
            width: 50%;
            font-size: 10px;
            line-height: 1.2;
        }

        .header-right {
            float: right;
            width: 50%;
            text-align: right;
            line-height: 0.8;
        }

        .logo-text {
            font-weight: bold;
            font-size: 11pt;
            color: #002d5b;
            letter-spacing: 0.5pt;
        }

        .user-name {
            font-size: 12pt;
            margin-top: 2mm;
        }

        .clear {
            clear: both;
        }

        .transit-type {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 2mm;
        }

        .field-group {
            margin-bottom: 1.5mm;
        }

        .field-label {
            font-size: 10px;
            color: #333;
            margin-bottom: 1mm;
        }

        .field-value {
            font-size: 11px;
            font-weight: bold;
            line-height: 1.1;
        }

        .small-field-group {
            margin-top: 5mm;
        }

        .small-row {
            margin-bottom: 2mm;
            font-size: 11px;
            vertical-align: middle;
        }

        .small-label {
            display: inline-block;
            width: 25mm;
            vertical-align: middle;
            height: 15px;
        }

        .small-value {
            font-weight: bold;
            vertical-align: middle;
            height: 15px;
            margin-bottom: 15px;
        }

        .footer-date {
            font-size: 11px;
            margin-bottom: 4mm;
        }

        i {
            font-family: 'Inter', sans-serif;
            font-size: 9px;
            color: #FF6B03;
            font-weight: 650;
        }
    </style>
</head>

<body>
    @php
        $totalWeight = $crr->packages->sum('weight');
        $totalPackages = $crr->packages->count();
    @endphp

    @foreach($crr->packages as $index => $pkg)
        <div class="label-page">
            <div class="header">
                <div class="header-left">
                    MarineCaddie India Private Limited<br>
                    +919560773375<br>
                    opsindia@marinecaddie.com
                </div>
                <div class="header-right">
                    <span
                        style="font-size: 22px; font-weight: bold; color:#002D5B; font-family: 'Inter', sans-serif;">Marine</span><span
                        style="font-size: 22px; font-weight: bold; color:#349DDA; font-family: 'Inter', sans-serif;">Caddie<br><i>Smart
                            Caddies, Smarter Logistics
                            !</i></span>
                </div>
                <div class="clear"></div>
            </div>

            <div class="field-group">
                <div class="field-label">Stock number</div>
                <div class="field-value">{{ $crr->stock_number }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">To</div>
                <div class="field-value"> MV {{ $crr->vessel_name ?: '—' }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Consignee</div>
                <div class="field-value" style="font-size: 12px;">{{ $crr->supplier ?: '—' }}</div>
            </div>
            <div class="field-group">
                <div class="field-label">Supplier</div>
                <div class="field-value" style="font-size: 12px;">{{ $crr->supplier ?: '—' }}</div>
            </div>

            <div class="small-field-group">
                <div class="small-row">
                    <span class="small-label">Pos</span>
                    <span class="small-value">{{ $pkg->warehouse_location ?: '—' }}</span>
                </div>

                <div class="small-row">
                    <span class="small-label">Pcs</span>
                    <span class="small-value"># {{ $index + 1 }} of {{ $totalPackages }}</span>
                </div>

                <div class="small-row">
                    <span class="small-label">Weight</span>
                    <span class="small-value">{{ number_format($pkg->weight, 0) }} of {{ number_format($totalWeight, 0) }}
                        KG</span>
                </div>

                <div class="small-row">
                    <span class="small-label">L/W/H (cm)</span>
                    <span
                        class="small-value">{{ number_format($pkg->length, 0) }}/{{ number_format($pkg->width, 0) }}/{{ number_format($pkg->height, 0) }}</span>
                </div>

                <div class="small-row">
                    <span class="small-label"
                        style="font-weight:bold; font-size:16px;">{{ strtoupper($crr->transit_type ?: 'ETL') }}</span>
                    <span class="small-value"
                        style="font-weight:bold; font-size:16px;">{{ $crr->customs_doc_reference ?: ($crr->transit_id ?: '—') }}</span>
                </div>
            </div>
        </div>
    @endforeach
</body>

</html>