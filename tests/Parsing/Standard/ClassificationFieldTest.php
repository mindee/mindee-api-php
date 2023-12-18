<?php

namespace Parsing\Standard;

use Mindee\Parsing\Standard\ClassificationField;
use PHPUnit\Framework\TestCase;

class ClassificationFieldTest extends TestCase
{
    public function testConstructor()
    {
        $fieldArray = [
            "value" => "automobile",
            "confidence" => 0.1
        ];
        $classification = new ClassificationField($fieldArray);
        $this->assertEquals("automobile", $classification->value);
        $this->assertEquals(0.1, $classification->confidence);
    }

    public function testConstructorNoClassificatio()
    {
        $fieldArray = [
            "value" => "N/A",
            "confidence" => 0.1
        ];
        $classification = new ClassificationField($fieldArray);
        $this->assertNull($classification->value);
    }
}
