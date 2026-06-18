<?php

declare(strict_types=1);

namespace ClientOptions;

use Mindee\ClientOptions\PollingOptions;
use PHPUnit\Framework\TestCase;

class PollingOptionsTest extends TestCase
{
    public function testConstructorNoArguments(): void
    {
        $pollingOptions = new PollingOptions();
        self::assertEquals(80, $pollingOptions->maxRetries);
        self::assertEquals(1.5, $pollingOptions->delaySec);
        self::assertEquals(2, $pollingOptions->initialDelaySec);
    }

    public function testConstructorSomeArguments(): void
    {
        $pollingOptions = new PollingOptions(maxRetries: 100);
        self::assertEquals(100, $pollingOptions->maxRetries);
        self::assertEquals(1.5, $pollingOptions->delaySec);
        self::assertEquals(2, $pollingOptions->initialDelaySec);
    }

    public function testConstructorAllArguments(): void
    {
        // voluntarily passing arguments in a different order than the constructor
        $pollingOptions = new PollingOptions(delaySec: 3.0, maxRetries: 100, initialDelaySec: 10);
        self::assertEquals(100, $pollingOptions->maxRetries);
        self::assertEquals(3, $pollingOptions->delaySec);
        self::assertEquals(10.0, $pollingOptions->initialDelaySec);
    }
}
