<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('formatWaNumber')) {
    /**
     * Format a phone number into a safe WhatsApp click-to-chat URL
     */
    function formatWaNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (empty($clean)) {
            return null;
        }

        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        return 'https://wa.me/' . $clean;
    }
}

if (!function_exists('sanitizeCsvField')) {
    /**
     * Neutralize CSV / Spreadsheet Formula Injection (CWE-1236)
     * Prepends single quote if the field begins with =, +, -, @, tab, or CR
     */
    function sanitizeCsvField($value): string
    {
        if ($value === null) {
            return '-';
        }

        $str = (string) $value;
        if ($str === '') {
            return '-';
        }

        $firstChar = $str[0];
        if (in_array($firstChar, ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $str;
        }

        return $str;
    }
}

if (!function_exists('toLowerTrim')) {
    /**
     * Safely trim and convert a string or nullable string to lowercase UTF-8
     */
    function toLowerTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        return mb_strtolower($trimmed, 'UTF-8');
    }
}

if (!function_exists('formatMapsUrl')) {
    /**
     * Format/normalize Google Maps link or coordinates into a valid clickable URL
     */
    function formatMapsUrl(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        $trimmed = trim($input);
        if ($trimmed === '') {
            return null;
        }

        // If user pasted an iframe embed code, extract src
        if (stripos($trimmed, '<iframe') !== false) {
            if (preg_match('/src=["\']([^"\']+)["\']/i', $trimmed, $matches)) {
                $trimmed = trim($matches[1]);
            }
        }

        // Check if already a valid full URL
        if (preg_match('#^https?://#i', $trimmed)) {
            return $trimmed;
        }

        // Check if starts with common maps domains without scheme
        if (preg_match('#^(maps\.app\.goo\.gl|goo\.gl/maps|maps\.google\.|www\.google\.[a-z.]+/maps)#i', $trimmed)) {
            return 'https://' . $trimmed;
        }

        // Check if coordinates format (e.g. -7.4244, 111.0234 or -7.4244,111.0234)
        if (preg_match('/^-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?$/', $trimmed)) {
            $coords = preg_replace('/\s+/', '', $trimmed);
            return 'https://www.google.com/maps?q=' . urlencode($coords);
        }

        // If looks like domain/path (contains dot and slash)
        if (preg_match('#^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(/.*)?$#', $trimmed)) {
            return 'https://' . $trimmed;
        }

        // Otherwise fallback: search query on Google Maps
        return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($trimmed);
    }
}

