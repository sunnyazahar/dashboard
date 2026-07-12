<?php

namespace App\Traits;

use App\Models\AdministrationChangeLog;
use App\Services\AdministrationChangeLogService;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait LogsFieldChanges
{
    public static function bootLogsFieldChanges(): void
    {
        static::created(function ($model) {
            app(AdministrationChangeLogService::class)->logCreated($model);
        });

        static::updated(function ($model) {
            app(AdministrationChangeLogService::class)->logChanges($model);
        });
    }

    public function changeLogs(): MorphMany
    {
        return $this->morphMany(AdministrationChangeLog::class, 'loggable')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    /**
     * Map of attribute => human label for change logging.
     * Empty array means log all non-skipped attributes with headline labels.
     */
    public function changeLogFieldLabels(): array
    {
        return [];
    }
}
