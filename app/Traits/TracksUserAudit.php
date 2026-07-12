<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait TracksUserAudit
{
    public static function bootTracksUserAudit(): void
    {
        static::creating(function ($model) {
            if (! auth()->check()) {
                return;
            }

            $userId = auth()->id();

            if (blank($model->created_by)) {
                $model->created_by = $userId;
            }

            if (blank($model->updated_by)) {
                $model->updated_by = $userId;
            }
        });

        static::updating(function ($model) {
            if (! auth()->check()) {
                return;
            }

            $userId = auth()->id();

            // Backfill creator on first edit for legacy rows.
            if (blank($model->getOriginal('created_by')) && blank($model->created_by)) {
                $model->created_by = $userId;
            }

            $model->updated_by = $userId;
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
