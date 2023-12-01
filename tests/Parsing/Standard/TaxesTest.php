<?php

namespace Parsing\Standard;

use Mindee\Parsing\Common\Ocr\Ocr;
use Mindee\Parsing\Standard\Taxes;
use Mindee\Parsing\Standard\TaxField;
use PHPUnit\Framework\TestCase;

class TaxesTest extends TestCase
{
    public function testConstructor()
    {
        $fieldArray = [
            "value" => 2,
            "rate" => 0.2,
            "code" => "QST",
            "confidence" => 0.1,
            "polygon" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]],
        ];
        $tax = new TaxField($fieldArray);
        $this->assertEquals(2, $tax->value);
        $this->assertEquals(0.1, $tax->confidence);
        $this->assertEquals(0.2, $tax->rate);
        $this->assertGreaterThan(0, count($tax->boundingBox->getCoordinates()));
        $this->assertEquals("Base: , Code: QST, Rate (%): 0.20, Amount: 2.00", strval($tax));
    }

    public function testConstructorNoRate(): void
    {
        $fieldDict = ["value" => 2.0, "confidence" => 0.1];
        $tax = new TaxField($fieldDict);
        $this->assertNull($tax->rate);
        $this->assertNull($tax->boundingBox);
        $this->assertEquals("Base: , Code: , Rate (%): , Amount: 2.00", (string)$tax);
    }

    public function testConstructorNoAmount(): void
    {
        $fieldDict = ["value" => "NA", "rate" => "AA", "code" => "N/A", "confidence" => 0.1];
        $tax = new TaxField($fieldDict);
        $this->assertNull($tax->value);
        $this->assertEquals("Base: , Code: , Rate (%): , Amount:", (string)$tax);
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
        $this->assertNull($tax->value);
        $this->assertEquals("Base: , Code: TAXES AND FEES, Rate (%): , Amount:", (string)$tax);
    }
}
