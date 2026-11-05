<?php

namespace App\Traits;

trait NormalizesEgyptianPhone
{
    /**
     * Clean and normalize an Egyptian phone number to standard 11-digit local format (01xxxxxxxxx).
     *
     * @param string|null $phone
     * @return string|null
     */
    public function normalizeEgyptianPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Strip all non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', (string)$phone);

        // Normalize international and local prefixes to standard 01xxxxxxxxx
        if (str_starts_with($cleaned, '00201')) {
            $cleaned = '01' . substr($cleaned, 5);
        } elseif (str_starts_with($cleaned, '201')) {
            $cleaned = '01' . substr($cleaned, 3);
        } elseif (str_starts_with($cleaned, '1') && strlen($cleaned) === 10) {
            $cleaned = '0' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Validate if the given phone conforms to Egyptian mobile telecom providers (010, 011, 012, 015).
     *
     * @param string|null $phone
     * @return bool
     */
    public function isValidEgyptianPhone(?string $phone): bool
    {
        $normalized = $this->normalizeEgyptianPhone($phone);

        if (!$normalized) {
            return false;
        }

        // Valid Egyptian mobile pattern: 01 followed by (0, 1, 2, 5) and 8 digits
        return (bool)preg_match('/^01[0125][0-9]{8}$/', $normalized);
    }

    /**
     * Format an Egyptian phone number for direct WhatsApp wa.me links (201xxxxxxxxx).
     *
     * @param string|null $phone
     * @return string|null
     */
    public function formatForWhatsApp(?string $phone): ?string
    {
        $normalized = $this->normalizeEgyptianPhone($phone);

        if (!$this->isValidEgyptianPhone($normalized)) {
            return null;
        }

        // Return country code 2 without plus sign
        return '2' . $normalized;
    }
}
