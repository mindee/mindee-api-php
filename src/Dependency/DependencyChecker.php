<?php

declare(strict_types=1);

namespace Mindee\Dependency;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeUnhandledException;

use function extension_loaded;

/**
 * Utility class to check the availability of potentially incompatible libraries.
 */
class DependencyChecker
{
    public static function isBernardLeditAvailable(): bool
    {
        return extension_loaded('bernard_ledit');
    }

    /** @throws MindeeUnhandledException */
    public static function requireBernardLedit(): void
    {
        if (!self::isBernardLeditAvailable()) {
            throw new MindeeUnhandledException(
                "To enable PDF and image features, install the 'bernard_ledit' PHP extension. "
                . "See https://github.com/mindee/bernard-ledit#php-binding",
                ErrorCode::USER_MISSING_DEPENDENCY
            );
        }
    }
}
