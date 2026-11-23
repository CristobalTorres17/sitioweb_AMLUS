<?php

namespace App\Helpers;

class CharacterEncodingHelper
{
    /**
     * Convert text from latin1 to UTF-8
     */
    public static function convertCharset(?string $text): ?string
    {
        if (empty($text)) {
            return $text;
        }

        // Detect if already UTF-8
        if (mb_detect_encoding($text, 'UTF-8', true)) {
            return $text;
        }

        // Convert from latin1 to UTF-8
        return mb_convert_encoding($text, 'UTF-8', 'latin1');
    }
}
