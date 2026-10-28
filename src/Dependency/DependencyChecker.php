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
    /**
     * The bernard_ledit version this SDK release is built and tested against. composer.json's "conflict"
     * pins the same version; DependencyCheckerTest checks that both agree.
     */
    public const BERNARD_LEDIT_VERSION = '0.1.0';

    /**
     * @throws MindeeUnhandledException Throws if bernard_ledit isn't loaded, or isn't the supported version.
     */
    public static function requireBernardLedit(): void
    {
        $install = sprintf('pie install mindee/bernard-ledit:%s', self::BERNARD_LEDIT_VERSION);
        if (!extension_loaded('bernard_ledit')) {
            throw new MindeeUnhandledException(
                "To enable PDF and image features, install the 'bernard_ledit' PHP extension: $install "
                . '(see https://github.com/mindee/bernard-ledit#php-binding)',
                ErrorCode::USER_MISSING_DEPENDENCY
            );
        }
        $installed = phpversion('bernard_ledit');
        if ($installed !== self::BERNARD_LEDIT_VERSION) {
            throw new MindeeUnhandledException(
                sprintf(
                    'This version of the Mindee SDK needs bernard_ledit %s, but %s is installed: %s',
                    self::BERNARD_LEDIT_VERSION,
                    $installed,
                    $install
                ),
                ErrorCode::USER_MISSING_DEPENDENCY
            );
        }
    }

    /**
     * Whether the bernard_ledit extension is loaded.
     */
    public static function isBernardLeditAvailable(): bool
    {
        return extension_loaded('bernard_ledit');
    }
}
