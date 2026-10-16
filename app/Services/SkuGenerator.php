<?php

namespace App\Services;

class SkuGenerator
{
    /**
     * Generate a SKU from a product name.
     *
     * Format: [WORD1]-[WORD2]-[WORD3]-[DDMMYY]
     *
     * Rules:
     * - Extract the first 3 uppercase letters from each of the first 3 words.
     * - Strip all non-alphabetic characters before processing.
     * - Pad short words (< 3 chars) with X to reach 3 characters.
     * - Use GEN as a placeholder for missing word slots.
     * - Append the current date as DDMMYY.
     *
     * Examples:
     *   "Men's Cotton Casual Shirt"  → MEN-COT-CAS-240726
     *   "Red Tea"                    → RED-TEA-GEN-240726
     *   "I Love PHP"                 → ILO-LOV-PHP-240726
     *   "A T"                        → ATX-GEN-GEN-240726
     */
    public static function generate(string $name): string
    {
        $cleaned = preg_replace('/[^a-zA-Z\s]/', '', $name);
        $cleaned = trim(preg_replace('/\s+/', ' ', $cleaned));

        $words = array_filter(explode(' ', $cleaned));

        $parts = [];
        for ($i = 0; $i < 3; $i++) {
            if (isset($words[$i])) {
                $parts[] = static::extractPrefix($words[$i]);
            } else {
                $parts[] = 'GEN';
            }
        }

        $date = date('dmy');

        return implode('-', [...$parts, $date]);
    }

    /**
     * Extract the first 3 uppercase characters from a word.
     * Pads with X if the word is shorter than 3 characters.
     */
    protected static function extractPrefix(string $word): string
    {
        $upper = strtoupper($word);
        $len = strlen($upper);

        if ($len >= 3) {
            return substr($upper, 0, 3);
        }

        return str_pad($upper, 3, 'X');
    }
}
