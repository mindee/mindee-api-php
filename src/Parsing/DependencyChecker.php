<?php

namespace Mindee\Parsing;

use Exception;
use Mindee\Error\MindeeUnhandledException;

/**
 * Utility class to check the availability of potentially incompatible libraries.
 */
class DependencyChecker
{
    /**
     * Returns true if ghostscript is available on the system.
     *
     * @return void
     * @throws MindeeUnhandledException Throws if the GhostScript command cannot be found on the system.
     */
    public static function isGhostscriptAvailable(): void
    {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $command32WasExecuted = (bool)shell_exec('which gswin32c.exe');
                $command64WasExecuted = (bool)shell_exec('which gswin64c.exe');
                $commandWasExecuted = $command32WasExecuted || $command64WasExecuted;
            } else {
                $commandWasExecuted = (bool)shell_exec('which gs');
            }
        } catch (Exception $e) {
            throw new MindeeUnhandledException(
                "To enable full support of PDF features, you need " .
                "to enable Ghostscript on your PHP installation."
            );
        }
        if (!$commandWasExecuted) {
            throw new MindeeUnhandledException(
                "To enable full support of PDF features, you need " .
                "to enable Ghostscript on your PHP installation."
            );
        }
    }

    /**
     * Returns true if ImageMagick is available on the system.
     *
     * @return void
     * @throws MindeeUnhandledException Throws if ImageMagick isn't loaded.
     */
    public static function isImageMagickAvailable(): void
    {
        if (!extension_loaded('imagick')) {
            throw new MindeeUnhandledException(
                "To enable full support of PDF features, you need " .
                "to enable ImageMagick on your PHP installation. Also, you " .
                "should setup ImageMagick's policy to allow for PDF operations."
            );
        }
    }

    /**
     * Checks whether Imagick is blocked by restrictive policy.
     *
     * @return void
     * @throws MindeeUnhandledException Throws if the local ImageMagick policy does not allow for PDF manipulations.
     */
    public static function isImageMagickPolicyAllowed(): void
    {
        self::isImageMagickAvailable();

        $imagick = new \Imagick();
        try {
            $imagick->readImage(
                (getenv('GITHUB_WORKSPACE') ?: ".") .
                "/tests/resources/products/expense_receipts/default_sample.jpg"
            );
        } catch (\Exception $e) {
            throw new MindeeUnhandledException(
                "To enable full support of PDF features, you need " .
                "to enable ImageMagick on your PHP installation. Also, you " .
                "should setup ImageMagick's policy to allow for PDF operations."
            );
        }
    }
}
