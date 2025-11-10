<?php

use PHPUnit\Framework\TestCase;

/**
 * Custom delay tests.
 * Note: Timers are purposefully kept loose due to frequent CI issues.
 */
class ClientUtilityMixinTest extends TestCase
{
    use Mindee\ClientUtilityMixin;

    public function testCustomSleep1Second(): void {
        $lowerBound = 1;
        $upperBound = 1.1;

        $start = microtime(true);
        $this->customSleep(1);
        $elapsed = microtime(true) - $start;
        $this->assertGreaterThanOrEqual($lowerBound, $elapsed);
        $this->assertLessThanOrEqual($upperBound, $elapsed);
    }

    public function testCustomSleep2Seconds(): void {
        $lowerBound = 2;
        $upperBound = 2.1;

        $start = microtime(true);
        $this->customSleep(2);
        $elapsed = microtime(true) - $start;
        $this->assertGreaterThanOrEqual($lowerBound, $elapsed);
        $this->assertLessThanOrEqual($upperBound, $elapsed);
    }

    public function testCustomSleep1dot5Seconds(): void {
        $lowerBound = 1.5;
        $upperBound = 1.6;

        $start = microtime(true);
        $this->customSleep(1.5);
        $elapsed = microtime(true) - $start;
        $this->assertGreaterThanOrEqual($lowerBound, $elapsed);
        $this->assertLessThanOrEqual($upperBound, $elapsed);
    }

    public function testCustomSleep0Seconds(): void {
        $start = microtime(true);
        $this->customSleep(0);
        $elapsed = microtime(true) - $start;
        $this->assertLessThanOrEqual(0.0001, $elapsed);
    }

    public function testCustomSleepMinus1Seconds(): void {
        $start = microtime(true);
        $this->customSleep(-1);
        $elapsed = microtime(true) - $start;
        $this->assertLessThanOrEqual(0.0001, $elapsed);
    }
}
