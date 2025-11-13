<?php

namespace V1\Standard;

use Mindee\Parsing\Standard\DateField;
use PHPUnit\Framework\TestCase;

class DateFieldTest extends TestCase
{
    public function testConstructor()
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
            "is_computed" => true
        ];
        $date = new DateField($fieldArray);
        $this->assertEquals("2018-04-01", $date->value);
        $this->assertInstanceOf(\DateTimeImmutable::class, $date->dateObject);
        $this->assertTrue($date->isComputed);
    }

    public function testConstructorNoDate()
    {
        $fieldArray = [
            "iso" => "N/A",
            "confidence" => 0.1
        ];
        $date = new DateField($fieldArray);
        $this->assertNull($date->value);
    }
}
