<?php

namespace Mindee;

trait CustomSleepMixin
{
    /**
     * Waits for a custom amount of time from either a float or an integer.
     * @param float|integer $delay Delay in seconds.
     * @return void
     */
    protected static function customSleep(float|int $delay): void
    {
        if ($delay <= 0) {
            return;
        }

        $seconds = intval($delay);
        $nanoseconds = (int) abs((float) $seconds - $delay);
        time_nanosleep($seconds, $nanoseconds * 1_000_000_000);
    }
}
