<?php

namespace V1\Standard;

use Mindee\Parsing\Standard\StringField;
use PHPUnit\Framework\TestCase;

class StringFieldTest extends TestCase
{
    public function testConstructor()
    {
        $fieldArray = [
            "polygon" => [
                [0.016, 0.707],
                [0.414, 0.707],
                [0.414, 0.831],
                [0.016, 0.831]
            ],
            "confidence" => 0.1,
            "value" => "some-value",
        ];

        $field = new StringField($fieldArray);
        $this->assertEquals("some-value", $field->value);
        $this->assertGreaterThan(0, count($field->boundingBox->getCoordinates()));
    }

    public function testConstructorFail()
    {
        $fieldArray = [
            "polygon" => null,
            "confidence" => 0.1,
            "value" => "N/A",
        ];

        $field = new StringField($fieldArray);
        $this->assertNull($field->value);
    }

    public function testConstructorNoRawValue()
    {
        $fieldArray = [
            "value" => "hello world",
            "confidence" => 0.1,
            "polygon" => [
                [0.016, 0.707],
                [0.414, 0.707],
                [0.414, 0.831],
                [0.016, 0.831],
            ],
        ];

        $field = new StringField($fieldArray);

        $this->assertEquals("hello world", $field->value);
        $this->assertNull($field->rawValue);
    }

    public function testConstructorRawValue()
    {
        $fieldArray = [
            "value" => "hello world",
            "raw_value" => "HelLO wOrld",
            "confidence" => 0.1,
            "polygon" => [
                [0.016, 0.707],
                [0.414, 0.707],
                [0.414, 0.831],
                [0.016, 0.831],
            ],
        ];

        $field = new StringField($fieldArray);

        $this->assertEquals("hello world", $field->value);
        $this->assertEquals("HelLO wOrld", $field->rawValue);
    }
}
