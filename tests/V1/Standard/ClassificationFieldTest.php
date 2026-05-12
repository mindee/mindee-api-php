<?php

declare(strict_types=1);

namespace V1\Standard;

use Mindee\V1\Parsing\Standard\ClassificationField;
use PHPUnit\Framework\TestCase;

class ClassificationFieldTest extends TestCase
{
    public function testConstructor(): void
    {
        $fieldArray = [
            "value" => "automobile",
            "confidence" => 0.1,
        ];
        $classification = new ClassificationField($fieldArray);
        self::assertSame("automobile", $classification->value);
        self::assertSame(0.1, $classification->confidence);
    }

    public function testConstructorNoClassificatio(): void
    {
        $fieldArray = [
            "value" => "N/A",
            "confidence" => 0.1,
        ];
        $classification = new ClassificationField($fieldArray);
        self::assertNull($classification->value);
    }
}
