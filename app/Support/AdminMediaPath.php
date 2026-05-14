<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class AdminMediaPath
{
    /**
     * Convert a public /storage URL or absolute URL to a storage disk-relative path.
     */
    public static function fromPublicUrl(string $url): string
    {
        $url = trim($url);
        $storagePrefix = '/storage/';

        if (Str::startsWith($url, ['http://', 'https://'])) {
            $url = parse_url($url, PHP_URL_PATH) ?: $url;
        }

        if (Str::startsWith($url, $storagePrefix)) {
            return ltrim(Str::after($url, $storagePrefix), '/');
        }

        $publicBaseUrl = Storage::disk('public')->url('');
        $normalized = str_replace('\\', '/', $url);
        $normalizedBase = str_replace('\\', '/', $publicBaseUrl);

        if ($normalizedBase !== '' && Str::startsWith($normalized, $normalizedBase)) {
            return ltrim(Str::after($normalized, $normalizedBase), '/');
        }

        return ltrim($normalized, '/');
    }
}
