<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Exception;

class ImageProcessingService
{
    protected ImageManager $imageManager;
    protected string $disk = 'public';

    // Configuration constants
    public const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    public const MIN_DIMENSION = 100;
    public const MAX_DIMENSION = 5000;

    public const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public const VARIANTS = [
        'thumbnail' => ['width' => 150, 'height' => 150],
        'medium' => ['width' => 500, 'height' => 500],
        'full' => ['width' => 1200, 'height' => 1200],
    ];

    public function __construct()
    {
        // Initialize with default driver (GD)
        $this->imageManager = ImageManager::gd();
    }

    /**
     * Set the disk to store images
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * Validate uploaded file
     *
     * @throws Exception
     */
    public function validate(UploadedFile $file): array
    {
        // Check MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new Exception('Invalid MIME type. Allowed: JPG, PNG, WebP');
        }

        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new Exception('File size exceeds 10MB limit');
        }

        // Check image dimensions
        try {
            $image = $this->imageManager->read($file->getRealPath());
            $width = $image->width();
            $height = $image->height();

            if ($width < self::MIN_DIMENSION || $height < self::MIN_DIMENSION) {
                throw new Exception("Image dimensions must be at least 100x100 pixels");
            }

            if ($width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION) {
                throw new Exception("Image dimensions cannot exceed 5000x5000 pixels");
            }

            return [
                'width' => $width,
                'height' => $height,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to read image: ' . $e->getMessage());
        }
    }

    /**
     * Process image and generate variants
     *
     * @return array Mapping of variant names to file paths
     */
    public function process(UploadedFile $file, string $directory = 'media'): array
    {
        // Validate first
        $metadata = $this->validate($file);

        // Generate unique filename
        $filename = uniqid('img_', true) . '.' . $file->getClientOriginalExtension();

        try {
            $image = $this->imageManager->read($file->getRealPath());
            $variants = [];

            // Generate original (PNG) and webp
            $originalPath = "{$directory}/original/{$filename}";
            Storage::disk($this->disk)->put($originalPath, $image->toPng()->toString(), 'public');
            $variants['original'] = ['png' => $originalPath];

            // Also write webp original
            $webpOriginalPath = preg_replace('/\.[^.]+$/', '.webp', $originalPath);
            Storage::disk($this->disk)->put($webpOriginalPath, $image->toWebp()->toString(), 'public');
            $variants['original']['webp'] = $webpOriginalPath;

            // Generate variants (PNG + WebP)
            foreach (self::VARIANTS as $variantName => $dimensions) {
                $variantPath = "{$directory}/{$variantName}/{$filename}";
                $resized = $this->resizePreservingAspect(
                    $this->imageManager->read($file->getRealPath()),
                    $dimensions['width'],
                    $dimensions['height']
                );

                // PNG
                Storage::disk($this->disk)->put($variantPath, $resized->toPng()->toString(), 'public');

                // WebP variant
                $variantWebpPath = preg_replace('/\.[^.]+$/', '.webp', $variantPath);
                Storage::disk($this->disk)->put($variantWebpPath, $resized->toWebp()->toString(), 'public');

                $variants[$variantName] = ['png' => $variantPath, 'webp' => $variantWebpPath];
            }

            return [
                'filename' => $filename,
                'variants' => $variants,
                'metadata' => $metadata,
                'disk' => $this->disk,
            ];
        } catch (Exception $e) {
            throw new Exception('Image processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Resize image preserving aspect ratio, fit inside max dimensions
     */
    protected function resizePreservingAspect($image, int $maxWidth, int $maxHeight)
    {
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // Calculate aspect ratio
        $aspectRatio = $originalWidth / $originalHeight;

        // Calculate new dimensions
        if ($originalWidth / $maxWidth > $originalHeight / $maxHeight) {
            // Width is the limiting factor
            $newWidth = $maxWidth;
            $newHeight = (int) ($maxWidth / $aspectRatio);
        } else {
            // Height is the limiting factor
            $newHeight = $maxHeight;
            $newWidth = (int) ($maxHeight * $aspectRatio);
        }

        return $image->resize($newWidth, $newHeight);
    }

    /**
     * Get URLs for all variants
     */
    public function getVariantUrls(string $directory, ?string $filename): array
    {
        // If filename is missing, return empty URLs so callers can handle placeholders
        if (empty($filename)) {
            $out = ['original' => ''];
            foreach (array_keys(self::VARIANTS) as $v) {
                $out[$v] = '';
            }
            return $out;
        }

        $urls = [];

        // Original — prefer webp, fall back to png path
        $urls['original'] = $this->getUrl("{$directory}/original/{$filename}");

        // Variants (thumbnail / medium / full)
        foreach (array_keys(self::VARIANTS) as $variantName) {
            $urls[$variantName] = $this->getUrl("{$directory}/{$variantName}/{$filename}");
        }

        return $urls;
    }

    /**
     * Get full URL for a file in storage
     */
    protected function getUrl(string $path): string
    {
        // For the local `public` disk return a relative `/storage/...` path so
        // the frontend does not pick up host/port from the environment (Vite dev server).
        if ($this->disk === 'public') {
            return '/storage/' . ltrim($path, '/');
        }

        // For S3 or other remote disks return the full URL provided by the disk driver
        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Delete all variants of an image
     */
    public function deleteVariants(string $directory, string $filename): bool
    {
        try {
            // Delete original
            Storage::disk($this->disk)->delete("{$directory}/original/{$filename}");

            // Delete variants
            foreach (array_keys(self::VARIANTS) as $variantName) {
                Storage::disk($this->disk)->delete("{$directory}/{$variantName}/{$filename}");
            }

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to delete image variants: ' . $e->getMessage());
        }
    }
}
