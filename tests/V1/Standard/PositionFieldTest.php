<?php

namespace V1\Standard;

use Mindee\Parsing\Standard\PositionField;
use PHPUnit\Framework\TestCase;

class PositionFieldTest extends TestCase
{
    public function testConstructor()
    {
        $fieldArray = [
            "bounding_box" => [
                [0.016, 0.707],
                [0.414, 0.707],
                [0.414, 0.831],
                [0.016, 0.831]
            ],
            "confidence" => 0.1,
            "quadrangle" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]],
            "polygon" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]],
            "rectangle" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]]
        ];

        $field = new PositionField($fieldArray);
        $this->assertEquals(4, count($field->value->getCoordinates()));
        $this->assertEquals(0.1, $field->confidence);
        $this->assertEquals(0.016, $field->polygon->getCoordinates()[0]->getX());
    }

    public function testConstructorFail()
    {
        $fieldArray = [
            "bounding_box" => [
                [0.016, 0.707],
                [0.414, 0.707],
                [0.414, 0.831],
                [0.016, 0.831]
            ],
            "confidence" => 0.1,
            "quadrangle" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]],
            "rectangle" => [[0.016, 0.707], [0.414, 0.707], [0.414, 0.831], [0.016, 0.831]]
        ];

        $field = new PositionField($fieldArray);
        $this->assertNull($field->value);
    }
}
