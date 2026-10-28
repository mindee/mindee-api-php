<?php

declare(strict_types=1);

namespace Mindee\Dependency;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeUnhandledException;

use function extension_loaded;
use function sprintf;

/**
 * Utility class to check the availability of potentially incompatible libraries.
 */
class DependencyChecker
{
    private const SUPPORTED_BERNARD_LEDIT = '0.1';

    /**
     * Whether the bernard_ledit extension is loaded.
     */
    public static function isBernardLeditAvailable(): bool
    {
        return extension_loaded('bernard_ledit');
    }

    /**
     * @throws MindeeUnhandledException Throws if the extension is missing or has an unsupported version.
     */
    public static function requireBernardLedit(): void
    {
        if (!self::isBernardLeditAvailable()) {
            throw new MindeeUnhandledException(
                "To enable PDF and image features, install the 'bernard_ledit' PHP extension: "
                . "pie install mindee/bernard-ledit:^" . self::SUPPORTED_BERNARD_LEDIT
                . " (see https://github.com/mindee/bernard-ledit#php-binding)",
                ErrorCode::USER_MISSING_DEPENDENCY
            );
        }
        $version = phpversion('bernard_ledit') ?: '0';
        if (!str_starts_with($version, self::SUPPORTED_BERNARD_LEDIT . '.')) {
            throw new MindeeUnhandledException(
                sprintf(
                    "bernard_ledit %s is installed, but this SDK supports %s.x: pie install mindee/bernard-ledit:^%s",
                    $version,
                    self::SUPPORTED_BERNARD_LEDIT,
                    self::SUPPORTED_BERNARD_LEDIT
                ),
                ErrorCode::USER_MISSING_DEPENDENCY
            );
        }
    }
}
