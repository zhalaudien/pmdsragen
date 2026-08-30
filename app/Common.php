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
