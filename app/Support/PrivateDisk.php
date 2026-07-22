<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class PrivateDisk
{
    public const NAME = 'private';

    public static function disk(): Filesystem
    {
        return Storage::disk(self::NAME);
    }

    public static function path(string $relativePath): string
    {
        $relativePath = trim($relativePath);

        if ($relativePath === '' || $relativePath === '0') {
            return self::disk()->path('__missing__');
        }

        $private = self::disk();

        if ($private->exists($relativePath)) {
            return $private->path($relativePath);
        }

        // Temporary fallback for files not yet migrated off the public disk.
        $public = Storage::disk('public');
        if ($public->exists($relativePath)) {
            return $public->path($relativePath);
        }

        return $private->path($relativePath);
    }

    public static function delete(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $private = self::disk();
        if ($private->exists($relativePath)) {
            $private->delete($relativePath);
        }

        $public = Storage::disk('public');
        if ($public->exists($relativePath)) {
            $public->delete($relativePath);
        }
    }

    public static function exists(string $relativePath): bool
    {
        if ($relativePath === '') {
            return false;
        }

        return self::disk()->exists($relativePath)
            || Storage::disk('public')->exists($relativePath);
    }
}
