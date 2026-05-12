<?php

declare(strict_types=1);

namespace V1\Standard;

use Mindee\V1\Parsing\Standard\TaxField;
use PHPUnit\Framework\TestCase;

use function count;

class TaxesTest extends TestCase
{
    public function testConstructor(): void
    {
        $fieldArray = [
            "value" => 2,
            "rate" => 0.2,
            "code" => "QST",
            "confidence" => 0.1,
            "polygon" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]],
        ];
        $tax = new TaxField($fieldArray);
        self::assertSame(2.0, $tax->value);
        self::assertSame(0.1, $tax->confidence);
        self::assertSame(0.2, $tax->rate);
        self::assertGreaterThan(0, count($tax->boundingBox->getCoordinates()));
        self::assertSame("Base: , Code: QST, Rate (%): 0.20, Amount: 2.00", (string) $tax);
    }

    public function testConstructorNoRate(): void
    {
        $fieldDict = ["value" => 2.0, "confidence" => 0.1];
        $tax = new TaxField($fieldDict);
        self::assertNull($tax->rate);
        self::assertNull($tax->boundingBox);
        self::assertSame("Base: , Code: , Rate (%): , Amount: 2.00", (string) $tax);
    }

    public function testConstructorNoAmount(): void
    {
        $fieldDict = ["value" => "NA", "rate" => "AA", "code" => "N/A", "confidence" => 0.1];
        $tax = new TaxField($fieldDict);
        self::assertNull($tax->value);
        self::assertSame("Base: , Code: , Rate (%): , Amount:", (string) $tax);
    }

    public function testConstructorOnlyCode(): void
    {
        $fieldDict = [
            "value" => "NA",
            "rate" => "None",
            "code" => "TAXES AND FEES",
            "confidence" => 0.1,
        ];
        $tax = new TaxField($fieldDict);
        self::assertNull($tax->value);
        self::assertSame("Base: , Code: TAXES AND FEES, Rate (%): , Amount:", (string) $tax);
    }
}
