@php
    /** @var \Illuminate\Database\Eloquent\Model|null $record */
    $record = $record ?? null;
    $bold = $bold ?? false;
@endphp
@if($record && ($record->created_at || $record->created_by))
    @php
        $createdBy = $record->creator?->name ?? '—';
        $changedBy = $record->updater?->name ?? ($record->creator?->name ?? '—');
        $createdOn = optional($record->created_at)->format('d.m.Y H:i') ?? '—';
        $changedOn = optional($record->updated_at)->format('d.m.Y H:i') ?? $createdOn;
    @endphp
    Created by {!! $bold ? '<b>'.e($createdBy).'</b>' : e($createdBy) !!} on {{ $createdOn }}<br>
    Last changed by {!! $bold ? '<b>'.e($changedBy).'</b>' : e($changedBy) !!} on {{ $changedOn }}
@endif
