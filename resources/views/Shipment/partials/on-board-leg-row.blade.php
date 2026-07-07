@php
    $rowIndex = $index ?? 0;
    $formatLegDate = function ($value) {
        if (!$value) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('d.m.Y');
        }

        return $value;
    };
@endphp
<div class="on-board-leg-row">
    <div class="on-board-leg-field">
        <div class="form-group-custom mb-0">
            <label>Departure date</label>
            <div class="input-with-icon">
                <input type="text" name="on_board_legs[{{ $rowIndex }}][departure_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('on_board_legs.' . $rowIndex . '.departure_date', isset($leg) ? $formatLegDate($leg->departure_date ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="on-board-leg-field">
        <div class="form-group-custom mb-0">
            <label>Delivery date</label>
            <div class="input-with-icon">
                <input type="text" name="on_board_legs[{{ $rowIndex }}][delivery_date]" class="form-control-sm-custom datepicker" placeholder="DD.MM.YYYY" value="{{ old('on_board_legs.' . $rowIndex . '.delivery_date', isset($leg) ? $formatLegDate($leg->delivery_date ?? null) : '') }}">
                <i class="ti-calendar"></i>
            </div>
        </div>
    </div>
    <div class="on-board-leg-field on-board-leg-field-time">
        <div class="form-group-custom mb-0">
            <label>Delivery time</label>
            <input type="text" name="on_board_legs[{{ $rowIndex }}][delivery_time]" class="form-control-sm-custom on-board-leg-time-input" placeholder="hh:mm" value="{{ old('on_board_legs.' . $rowIndex . '.delivery_time', isset($leg) ? ($leg->delivery_time ?? '') : '') }}">
        </div>
    </div>
    <button type="button" class="btn btn-link text-muted p-0 on-board-leg-remove-btn remove-on-board-leg" title="Remove">
        <i class="ti-close" style="font-size: 14px;"></i>
    </button>
</div>
