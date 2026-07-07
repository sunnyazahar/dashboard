<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class JsonArrayCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?array
    {
        return self::decode($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return json_encode(array_values($value));
        }

        $decoded = self::decode($value);

        return $decoded === null ? (string) $value : json_encode(array_values($decoded));
    }

    public static function decode(mixed $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $current = $value;

        for ($i = 0; $i < 3; $i++) {
            $decoded = json_decode($current, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                break;
            }

            if (is_array($decoded)) {
                return $decoded;
            }

            if (!is_string($decoded)) {
                break;
            }

            $current = $decoded;
        }

        $parts = preg_split('/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY);

        return $parts ?: null;
    }
}
