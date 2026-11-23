<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class BannerApiController extends Controller
{
    /**
     * Get banners for a specific page (public API)
     *
     * @param string $page The page slug (e.g., 'home', 'services')
     * @return JsonResponse
     */
    public function getByPage(string $page): JsonResponse
    {
        // First, try to get banners for the requested page
        $banners = Banner::where('page', $page)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // If no banners found and page is not 'home', fallback to 'home' banners
        if ($banners->isEmpty() && $page !== 'home') {
            $banners = Banner::where('page', 'home')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }

        // Transform banners to include full image URLs
        $data = $banners->map(function (Banner $banner) {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'subtitle' => $banner->subtitle,
                'image_path' => $this->getImageUrl($banner->image_path),
                'cta_text' => $banner->cta_text,
                'cta_url' => $banner->cta_url,
                'show_button' => (bool) $banner->show_button,
                'page' => $banner->page,
                'sort_order' => $banner->sort_order,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => $data->count(),
        ]);
    }

    /**
     * Get full URL for banner image
     *
     * @param string|null $imagePath
     * @return string|null
     */
    private function getImageUrl(?string $imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }

        // If the path already starts with 'http', return as-is
        if (str_starts_with($imagePath, 'http')) {
            return $imagePath;
        }

        // Return full URL for storage path
        return Storage::disk('public')->url($imagePath);
    }
}
