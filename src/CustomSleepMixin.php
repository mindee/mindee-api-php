<?php

namespace Mindee;

trait CustomSleepMixin
{
    /**
     * Waits for a custom amount of time from either a float or an integer.
     * Purposefully waits for one more millisecond on windows due to flakiness in delays between OS.
     * @param float|integer $delay Delay in seconds.
     * @return void
     */
    protected static function customSleep(float|int $delay): void
    {
        if ($delay <= 0) {
            return;
        }

        $seconds = intval($delay);
        $nanoseconds = abs($seconds - (float) $delay);
        if (
            strtoupper(substr(PHP_OS_FAMILY, 0, 7)) === 'WINDOWS'
        ) {
            usleep(1000);
        }
        time_nanosleep($seconds, (int) ($nanoseconds * 1_000_000_000));
    }
}
