@php
    /** @var \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $changeLogs */
    $changeLogs = $changeLogs ?? collect();
@endphp
<div class="admin-change-log-panel" style="margin: 16px 0 80px; background: #fff; border: 1px solid #e5e7eb; border-radius: 4px;">
    <div style="padding: 10px 16px; border-bottom: 1px solid #e5e7eb; font-size: 13px; font-weight: 600; color: #1b5e6f;">
        Change log
    </div>
    <div style="padding: 12px 16px; max-height: 280px; overflow-y: auto;">
        @forelse ($changeLogs as $changeLog)
            <div style="border-bottom: 1px solid #f8fafc; padding: 8px 0;">
                <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 11px; font-weight: 600; color: #0ea5e9;">{{ $changeLog->title }}</div>
                        @if ($changeLog->description)
                            <div style="font-size: 10px; color: #64748b; margin-top: 2px;">{{ $changeLog->description }}</div>
                        @endif
                    </div>
                    <div style="text-align: right; white-space: nowrap;">
                        <div style="font-size: 11px; color: #334155; font-weight: 500;">{{ $changeLog->user?->name ?? 'System' }}</div>
                        <div style="font-size: 10px; color: #94a3b8;">{{ optional($changeLog->created_at)->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
            </div>
        @empty
            <div style="font-size: 12px; color: #64748b; text-align: center; padding: 18px 0;">
                No changes recorded yet.
            </div>
        @endforelse
    </div>
</div>
