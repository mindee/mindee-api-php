<?php

declare(strict_types=1);

namespace V1\Standard;

use Mindee\V1\Parsing\Standard\DateField;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class DateFieldTest extends TestCase
{
    public function testConstructor(): void
    {
        $fieldArray = [
            "value" => "2018-04-01",
            "confidence" => 0.1,
            "polygon" => [
                [0.016, 0.707],
                [0.414, 0.707],
                [0.414, 0.831],
                [0.016, 0.831],
            ],
            "is_computed" => true,
        ];
        $date = new DateField($fieldArray);
        self::assertSame("2018-04-01", $date->value);
        self::assertInstanceOf(DateTimeImmutable::class, $date->dateObject);
        self::assertTrue($date->isComputed);
    }

    public function testConstructorNoDate(): void
    {
        $fieldArray = [
            "iso" => "N/A",
            "confidence" => 0.1,
        ];
        $date = new DateField($fieldArray);
        self::assertNull($date->value);
    }
}
