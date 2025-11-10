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
        $micros = $delay - $seconds;
        if ($seconds > 0) {
            sleep($delay);
        }
        if ($micros > 0) {
            usleep($micros * 1_000_000);
        }
    }
}
