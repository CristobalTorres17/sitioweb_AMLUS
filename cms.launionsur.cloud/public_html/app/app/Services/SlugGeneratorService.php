<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Str;

class SlugGeneratorService
{
    /**
     * Generate a unique slug from a title.
     *
     * @param string $title
     * @param int|null $excludePageId - Page ID to exclude from uniqueness check (for updates)
     * @return string
     */
    public function generateSlug(string $title, ?int $excludePageId = null): string
    {
        $slug = Str::slug($title);

        return $this->ensureUnique($slug, $excludePageId);
    }

    /**
     * Ensure slug is unique in the database.
     *
     * @param string $slug
     * @param int|null $excludePageId
     * @return string
     */
    private function ensureUnique(string $slug, ?int $excludePageId = null): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludePageId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists in database.
     *
     * @param string $slug
     * @param int|null $excludePageId
     * @return bool
     */
    private function slugExists(string $slug, ?int $excludePageId = null): bool
    {
        $query = Page::where('slug', $slug);

        if ($excludePageId) {
            $query->where('id', '!=', $excludePageId);
        }

        return $query->exists();
    }

    /**
     * Validate if a slug is SEO-friendly.
     *
     * @param string $slug
     * @return bool
     */
    public function isValidSlug(string $slug): bool
    {
        // Must be lowercase, contain only alphanumeric and hyphens
        // Must not start or end with hyphen
        // Must be between 3 and 255 characters
        $pattern = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

        return strlen($slug) >= 3 &&
            strlen($slug) <= 255 &&
            preg_match($pattern, $slug) === 1;
    }

    /**
     * Validate title length.
     *
     * @param string $title
     * @return bool
     */
    public function isValidTitle(string $title): bool
    {
        $length = strlen($title);

        return $length >= 3 && $length <= 255;
    }

    /**
     * Get slug generation suggestions from a title.
     *
     * @param string $title
     * @return array
     */
    public function getSuggestions(string $title, int $count = 3): array
    {
        $suggestions = [];
        $slug = Str::slug($title);

        // First suggestion: original
        $suggestions[] = $this->ensureUnique($slug);

        // Additional suggestions with variations
        for ($i = 1; $i < $count && $i < 3; $i++) {
            $variant = substr($slug, 0, strlen($slug) - max(1, strlen($slug) - 10)) . '-' . $i;
            $suggestions[] = $this->ensureUnique($variant);
        }

        return array_unique($suggestions);
    }
}
