<?php

namespace App\Support;

class MediaUrlResolver
{
    private static function buildUrl(string $baseUrl, string $path): string
    {
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    private static function normalizeCandidatePath(string $candidate): string
    {
        $candidate = trim($candidate);

        if ($candidate === '') {
            return $candidate;
        }

        if (str_starts_with($candidate, '/storage/')) {
            $candidate = ltrim(substr($candidate, strlen('/storage/')), '/');
        }

        if (str_starts_with($candidate, 'media/')) {
            $relativePath = substr($candidate, strlen('media/'));

            if ($relativePath !== '' && !str_contains($relativePath, '/')) {
                return 'media/original/' . $relativePath;
            }
        }

        if (!str_contains($candidate, '/')) {
            return 'media/original/' . $candidate;
        }

        return $candidate;
    }

    public static function resolve(mixed $media, ?string $fallback = null): ?string
    {
        if (!$media) {
            return $fallback;
        }

        $candidate = is_string($media)
            ? trim($media)
            : (data_get($media, 'path') ?:
                data_get($media, 'file_path') ?:
                data_get($media, 'storage_path') ?:
                data_get($media, 'disk_path') ?:
                data_get($media, 'url'));

        if (!$candidate) {
            return $fallback;
        }

        if (
            str_starts_with($candidate, 'http://') ||
            str_starts_with($candidate, 'https://') ||
            str_starts_with($candidate, '//')
        ) {
            return $candidate;
        }

        $candidate = self::normalizeCandidatePath($candidate);

        $cmsMediaBase = rtrim((string) config('filesystems.disks.cms_public.url', url('/cms-media')), '/');

        if (str_starts_with($candidate, '/storage/')) {
            return $cmsMediaBase . substr($candidate, strlen('/storage'));
        }

        if (str_starts_with($candidate, '/cms-media/')) {
            return $candidate;
        }

        if (str_starts_with($candidate, '/')) {
            return $candidate;
        }

        return self::buildUrl($cmsMediaBase, $candidate);
    }
}
