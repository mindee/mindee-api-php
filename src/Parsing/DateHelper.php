<?php

declare(strict_types=1);

namespace Mindee\Parsing;

use DateTime;
use DateTimeImmutable;
use Exception;

/**
 * Utility class to parse date strings returned by the API.
 */
class DateHelper
{
    /**
     * Parse a date string into a DateTime object.
     *
     * @param string|null $dateString Date string to parse.
     * @return DateTime|null Parsed date, or null if empty/invalid.
     */
    public static function parseDate(?string $dateString): ?DateTime
    {
        if (empty($dateString)) {
            return null;
        }
        try {
            return new DateTime($dateString);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Parse a date string into a DateTimeImmutable object.
     *
     * @param string|null $dateString Date string to parse.
     * @return DateTimeImmutable|null Parsed date, or null if empty/invalid.
     */
    public static function parseDateImmutable(?string $dateString): ?DateTimeImmutable
    {
        if (empty($dateString)) {
            return null;
        }
        try {
            return new DateTimeImmutable($dateString);
        } catch (Exception) {
            return null;
        }
    }
}
