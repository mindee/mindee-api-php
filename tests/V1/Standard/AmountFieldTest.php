<?php

namespace V1\Standard;

use Mindee\Parsing\Standard\AmountField;
use PHPUnit\Framework\TestCase;

class AmountFieldTest extends TestCase
{
    public function testConstructor()
    {
        $fieldArray = [
            "value" => "2",
            "confidence" => 0.1,
            "polygon" => [
                [0.016, 0.707],
                [0.414, 0.707],
                [0.414, 0.831],
                [0.016, 0.831],
            ]
        ];
        $amount = new AmountField($fieldArray);
        $this->assertEquals(2, $amount->value);
    }

    public function testConstructorNoAmount()
    {
        $fieldArray = [
            "value" => "N/A",
            "confidence" => 0.1
        ];
        $amount = new AmountField($fieldArray);
        $this->assertNull($amount->value);
    }
}
