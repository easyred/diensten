<?php

if (!function_exists('format_phone_number')) {
    /**
     * Format phone/WhatsApp number with + prefix
     * 
     * @param string|null $number
     * @return string
     */
    function format_phone_number($number)
    {
        if (empty($number) || $number === '—' || $number === 'N/A' || $number === 'Niet opgegeven' || $number === 'Niet ingesteld') {
            return $number;
        }

        // Remove all non-digit characters
        $cleaned = preg_replace('/\D/', '', $number);
        
        // If empty after cleaning, return original
        if (empty($cleaned)) {
            return $number;
        }

        // If it already starts with +, return as is
        if (strpos($number, '+') === 0) {
            return $number;
        }

        // Add + prefix
        return '+' . $cleaned;
    }
}

