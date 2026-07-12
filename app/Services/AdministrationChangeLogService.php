<?php

namespace App\Services;

use App\Models\AdministrationChangeLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdministrationChangeLogService
{
    private const SKIP_FIELDS = [
        'id',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'scan_gun_password',
        'scangun_password',
        'logo',
    ];

    public function log(Model $model, string $title, ?string $description = null, ?string $field = null): AdministrationChangeLog
    {
        return AdministrationChangeLog::create([
            'loggable_type' => $model->getMorphClass(),
            'loggable_id' => $model->getKey(),
            'user_id' => auth()->id(),
            'field' => $field,
            'title' => $title,
            'description' => $description,
            'created_at' => now(),
        ]);
    }

    public function logCreated(Model $model): void
    {
        $this->log($model, $this->entityLabel($model) . ' created');
    }

    public function logChanges(Model $model): void
    {
        $labels = method_exists($model, 'changeLogFieldLabels')
            ? $model->changeLogFieldLabels()
            : [];

        $extraSkip = method_exists($model, 'changeLogSkipFields')
            ? $model->changeLogSkipFields()
            : [];

        $skip = array_merge(self::SKIP_FIELDS, $extraSkip);
        $changed = array_keys($model->getChanges());

        foreach ($changed as $field) {
            if (in_array($field, $skip, true)) {
                continue;
            }

            if ($labels !== [] && ! array_key_exists($field, $labels)) {
                continue;
            }

            $label = $labels[$field] ?? $this->humanize($field);
            $old = $model->getOriginal($field);
            $new = $model->getAttribute($field);

            if ($this->valuesEqual($old, $new)) {
                continue;
            }

            $description = $this->fromToDescription(
                $this->formatValue($old),
                $this->formatValue($new)
            );

            if ($description === '') {
                continue;
            }

            $this->log($model, $label . ' edited', $description, $field);
        }
    }

    /**
     * Compare before/after attribute maps and write change logs against $model.
     * Returns true when at least one log entry was created.
     */
    public function logMappedChanges(Model $model, array $before, array $after, array $labels = []): bool
    {
        $logged = false;
        $fields = array_unique(array_merge(array_keys($before), array_keys($after)));

        foreach ($fields as $field) {
            if (in_array($field, self::SKIP_FIELDS, true)) {
                continue;
            }

            if ($labels !== [] && ! array_key_exists($field, $labels)) {
                continue;
            }

            $old = $before[$field] ?? null;
            $new = $after[$field] ?? null;

            if ($this->valuesEqual($old, $new)) {
                continue;
            }

            $label = $labels[$field] ?? $this->humanize((string) $field);
            $description = $this->fromToDescription(
                $this->formatValue($old),
                $this->formatValue($new)
            );

            if ($description === '') {
                continue;
            }

            $this->log($model, $label . ' edited', $description, (string) $field);
            $logged = true;
        }

        return $logged;
    }

    private function entityLabel(Model $model): string
    {
        return Str::headline(class_basename($model));
    }

    private function humanize(string $field): string
    {
        return Str::headline(str_replace('_', ' ', $field));
    }

    private function formatValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'empty';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('d.m.Y H:i');
        }

        if (is_array($value)) {
            $flat = array_filter(array_map(
                fn ($item) => trim((string) $item),
                $value
            ), fn ($item) => $item !== '');

            return $flat !== [] ? $this->truncate(implode(', ', $flat)) : 'empty';
        }

        $string = trim((string) $value);

        if ($string === '') {
            return 'empty';
        }

        // Detect date-like strings
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $string)) {
            try {
                return Carbon::parse($string)->format('d.m.Y');
            } catch (\Throwable) {
                // fall through
            }
        }

        return $this->truncate($string);
    }

    private function fromToDescription(string $old, string $new): string
    {
        if ($old === $new) {
            return '';
        }

        return 'From ' . $old . ' to ' . $new;
    }

    private function valuesEqual(mixed $left, mixed $right): bool
    {
        if (is_array($left) || is_array($right)) {
            return json_encode($left) === json_encode($right);
        }

        if (is_bool($left) || is_bool($right)) {
            return (bool) $left === (bool) $right;
        }

        return (string) ($left ?? '') === (string) ($right ?? '');
    }

    private function truncate(string $value, int $limit = 180): string
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');

        if ($value === '') {
            return 'empty';
        }

        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, $limit - 3) . '...';
    }
}
