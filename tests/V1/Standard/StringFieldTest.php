<?php

declare(strict_types=1);

namespace V1\Standard;

use Mindee\V1\Parsing\Standard\StringField;
use PHPUnit\Framework\TestCase;

use function count;

class StringFieldTest extends TestCase
{
    public function testConstructor(): void
    {
        $fieldArray = [
            "polygon" => [
                [0.016, 0.707],
                [0.414, 0.707],
                [0.414, 0.831],
                [0.016, 0.831],
            ],
            "confidence" => 0.1,
            "value" => "some-value",
        ];

        $field = new StringField($fieldArray);
        self::assertSame("some-value", $field->value);
        self::assertGreaterThan(0, count($field->boundingBox->getCoordinates()));
    }

    public function testConstructorFail(): void
    {
        $fieldArray = [
            "polygon" => null,
            "confidence" => 0.1,
            "value" => "N/A",
        ];

        $field = new StringField($fieldArray);
        self::assertNull($field->value);
    }

    public function testConstructorNoRawValue(): void
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

        self::assertSame("hello world", $field->value);
        self::assertNull($field->rawValue);
    }

    public function testConstructorRawValue(): void
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

        self::assertSame("hello world", $field->value);
        self::assertSame("HelLO wOrld", $field->rawValue);
    }
}
