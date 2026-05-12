<?php

declare(strict_types=1);

namespace V1\Standard;

use Mindee\V1\Parsing\Standard\AmountField;
use PHPUnit\Framework\TestCase;

class AmountFieldTest extends TestCase
{
    public function testConstructor(): void
    {
        $fieldArray = [
            "value" => "2",
            "confidence" => 0.1,
            "polygon" => [
                [0.016, 0.707],
                [0.414, 0.707],
                [0.414, 0.831],
                [0.016, 0.831],
            ],
        ];
        $amount = new AmountField($fieldArray);
        self::assertSame(2.0, $amount->value);
    }

    public function testConstructorNoAmount(): void
    {
        $fieldArray = [
            "value" => "N/A",
            "confidence" => 0.1,
        ];
        $amount = new AmountField($fieldArray);
        self::assertNull($amount->value);
    }
}
