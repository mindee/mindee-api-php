<?php

declare(strict_types=1);

namespace Mindee;

use Mindee\Error\MindeeException;
use Mindee\Http\CancellationToken;

trait CustomSleepMixin
{
    /**
     * Waits for a custom amount of time from either a float or an integer.
     * Purposefully waits for one more millisecond on Windows due to flakiness in delays between OS.
     * @param float|integer $delay Delay in seconds.
     * @param CancellationToken|null $cancellationToken CancellationToken to check for cancellation.
     */
    protected static function customSleep(float|int $delay, ?CancellationToken $cancellationToken = null): void
    {
        if ($delay <= 0) {
            return;
        }
        $endTime = microtime(true) + $delay;
        $pollIntervalMicroseconds = 100_000;
        while (microtime(true) < $endTime) {
            if ($cancellationToken && $cancellationToken->isCancelled()) {
                throw new MindeeException("Polling operation was cancelled.");
            }
            $remainingSeconds = $endTime - microtime(true);
            if ($remainingSeconds <= 0) {
                break;
            }
            $sleepMicroseconds = (int) min($pollIntervalMicroseconds, $remainingSeconds * 1_000_000);
            usleep($sleepMicroseconds);
        }
        if (PHP_OS_FAMILY === 'Windows') {
            usleep(1000);
        }
    }
}
