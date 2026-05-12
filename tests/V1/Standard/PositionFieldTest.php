<?php

declare(strict_types=1);

namespace V1\Standard;

use Mindee\V1\Parsing\Standard\PositionField;
use PHPUnit\Framework\TestCase;

class PositionFieldTest extends TestCase
{
    public function testConstructor(): void
    {
        $fieldArray = [
            "bounding_box" => [
                [0.016, 0.707],
                [0.414, 0.707],
                [0.414, 0.831],
                [0.016, 0.831],
            ],
            "confidence" => 0.1,
            "quadrangle" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]],
            "polygon" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]],
            "rectangle" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]],
        ];

        $field = new PositionField($fieldArray);
        self::assertCount(4, $field->value->getCoordinates());
        self::assertSame(0.1, $field->confidence);
        self::assertSame(0.016, $field->polygon->getCoordinates()[0]->getX());
    }

    public function testConstructorFail(): void
    {
        $fieldArray = [
            "bounding_box" => [
                [0.016, 0.707],
                [0.414, 0.707],
                [0.414, 0.831],
                [0.016, 0.831],
            ],
            "confidence" => 0.1,
            "quadrangle" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]],
            "rectangle" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]],
        ];

        $field = new PositionField($fieldArray);
        self::assertNull($field->value);
    }
}
