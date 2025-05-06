<?php

namespace App\Support;

use Illuminate\Support\Str;


class EmbeddingTextSanitizer
{
    protected static array $defaultStopWords = [
        'a', 'an', 'the', 'and', 'or', 'but', 'if', 'in', 'on', 'with',
        'at', 'by', 'for', 'to', 'from', 'of', 'is', 'are', 'was', 'were',
        'be', 'been', 'being', 'has', 'have', 'had', 'do', 'does', 'did',
        'will', 'would', 'shall', 'should', 'can', 'could', 'may', 'might', 'must'
    ];

    public static function sanitize(string $text, array $extraStopWords = []): string
    {
        $stopWords = array_merge(self::$defaultStopWords, $extraStopWords);

        return collect(
                
                preg_split('/\s+/', Str::of($text)
                    ->lower()
                    ->replaceMatches('/[^\w\s]/u', '')
                )
            )
            ->reject(fn($word) => in_array($word, $stopWords))
            ->implode(' ');
    }
}
