<?php

namespace Mindee\Parsing;

use Exception;

/**
 * Utility class to check the availability of potentially incompatible libraries.
 */
class DependencyChecker
{
    /**
     * Returns true if ghostscript is available on the system.
     *
     * @return boolean
     */
    public static function isGhostscriptAvailable(): bool
    {
        try {
            return (bool)shell_exec('which gs');
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns true if ImageMagick is available on the system.
     *
     * @return boolean
     */
    public static function isImageMagickAvailable(): bool
    {
        return extension_loaded('imagick');
    }

    /**
     * Checks whether Imagick is blocked by restrictive policy.
     *
     * @return boolean
     */
    public static function isImageMagickPolicyAllowed(): bool
    {
        if (!self::isImageMagickAvailable()) {
            return false;
        }

        $imagick = new \Imagick();
        try {
            $imagick->readImage(
                (getenv('GITHUB_WORKSPACE') ?: ".") .
                "/tests/resources/products/expense_receipts/default_sample.jpg"
            );
            return true;
        } catch (\ImagickException $e) {
            return false;
        }
    }

    /**
     * @return boolean True if full PDF handling features are available.
     */
    public static function isFullPdfHandlingAvailable(): bool
    {
        if (!self::isGhostscriptAvailable()) {
            return false;
        }
        if (!self::isImageMagickPolicyAllowed()) {
            return false;
        }
        return true;
    }
}
