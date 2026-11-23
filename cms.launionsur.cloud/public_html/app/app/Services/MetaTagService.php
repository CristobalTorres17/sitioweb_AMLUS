<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Media;
use Illuminate\Support\Str;

class MetaTagService
{
    /**
     * Generate complete meta tags array for a page
     *
     * @param Page $page
     * @param string $url Full URL of the page
     * @return array{
     *     canonical: string,
     *     title: string,
     *     description: string,
     *     keywords: string,
     *     robots: string,
     *     og_title: string,
     *     og_description: string,
     *     og_image: string|null,
     *     og_url: string,
     *     og_type: string,
     *     twitter_card: string,
     *     twitter_title: string,
     *     twitter_description: string,
     *     twitter_image: string|null,
     *     breadcrumb_schema: string
     * }
     */
    public static function generateMetaTags(Page $page, string $url): array
    {
        return [
            'canonical' => self::getCanonicalUrl($url),
            'title' => self::sanitizeTitle($page->title),
            'description' => self::getDescription($page),
            'keywords' => self::sanitizeKeywords($page->meta_keywords),
            'robots' => 'index, follow',

            // Open Graph Tags
            'og_title' => self::sanitizeTitle($page->title),
            'og_description' => self::getDescription($page),
            'og_image' => self::getOGImage($page),
            'og_url' => self::getCanonicalUrl($url),
            'og_type' => 'article',

            // Twitter Card Tags
            'twitter_card' => 'summary_large_image',
            'twitter_title' => self::sanitizeTitle($page->title),
            'twitter_description' => self::getDescription($page),
            'twitter_image' => self::getOGImage($page),

            // Structured Data
            'breadcrumb_schema' => self::generateBreadcrumbSchema($page, $url),
        ];
    }

    /**
     * Get page meta description (from meta_description or auto-truncate content)
     */
    public static function getDescription(Page $page): string
    {
        if ($page->meta_description) {
            return self::sanitizeDescription($page->meta_description);
        }

        // Fallback: extract from content
        if ($page->description) {
            return self::sanitizeDescription($page->description);
        }

        // Last resort: strip HTML from content and truncate
        $text = strip_tags($page->content);
        return self::sanitizeDescription(Str::limit($text, 160, ''));
    }

    /**
     * Get OG image URL from page's og_image media
     */
    public static function getOGImage(Page $page): ?string
    {
        if (!$page->og_image_media_id || !$page->ogImage) {
            return self::getDefaultOGImage();
        }

        $media = $page->ogImage;

        // Returns full URL to the media file
        return match ($media->disk ?? 'public') {
            'public' => asset('storage/' . $media->path),
            's3' => $media->url, // Assume S3 URL is already complete
            default => asset('storage/' . $media->path),
        };
    }

    /**
     * Get default OG image (site logo or null)
     */
    public static function getDefaultOGImage(): ?string
    {
        // Could be site logo stored in env or config
        $defaultImage = config('app.og_image');
        return $defaultImage ? asset($defaultImage) : null;
    }

    /**
     * Get canonical URL for the page
     */
    public static function getCanonicalUrl(string $url): string
    {
        // Remove query parameters and fragments
        return strtok($url, '?#');
    }

    /**
     * Generate JSON-LD breadcrumb schema
     */
    public static function generateBreadcrumbSchema(Page $page, string $url): string
    {
        $breadcrumbs = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => config('app.name', 'Site'),
                'item' => url('/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $page->title,
                'item' => $url,
            ],
        ];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs,
        ];

        return json_encode($schema);
    }

    /**
     * Sanitize title for meta tags (max 60 chars)
     */
    protected static function sanitizeTitle(string $title): string
    {
        $title = strip_tags($title);
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        return Str::limit($title, 60, '');
    }

    /**
     * Sanitize description for meta tags (max 160 chars)
     */
    protected static function sanitizeDescription(string $description): string
    {
        $description = strip_tags($description);
        $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
        return Str::limit($description, 160, '');
    }

    /**
     * Sanitize and format keywords
     */
    protected static function sanitizeKeywords(string $keywords): string
    {
        if (empty($keywords)) {
            return '';
        }

        // Split by comma, trim, remove empty, lowercase
        $words = array_filter(
            array_map('trim', explode(',', $keywords)),
            fn($word) => !empty($word)
        );

        return implode(', ', array_slice($words, 0, 10)); // Max 10 keywords
    }

    /**
     * Validate meta description length (160 chars is optimal)
     */
    public static function validateMetaDescription(string $description): array
    {
        $length = strlen($description);

        return [
            'is_valid' => $length >= 120 && $length <= 160,
            'length' => $length,
            'warning' => match (true) {
                $length < 120 => 'Meta description too short (< 120 chars)',
                $length > 160 => 'Meta description too long (> 160 chars)',
                default => null,
            },
        ];
    }

    /**
     * Get structured meta data for API responses
     */
    public static function getPageMeta(Page $page, string $url): array
    {
        return [
            'title' => $page->title,
            'description' => self::getDescription($page),
            'image' => self::getOGImage($page),
            'url' => $url,
            'published_at' => $page->published_at?->toIso8601String(),
            'updated_at' => $page->updated_at?->toIso8601String(),
            'author' => $page->creator?->name,
        ];
    }
}
