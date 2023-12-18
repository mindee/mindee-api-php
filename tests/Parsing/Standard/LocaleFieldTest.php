<?php

namespace Parsing\Standard;

use Mindee\Parsing\Standard\LocaleField;
use PHPUnit\Framework\TestCase;

class LocaleFieldTest extends TestCase
{
    public function testConstructor()
    {
        $fieldArray = [
            "confidence" => 0.82,
            "country" => "GB",
            "currency" => "GBP",
            "language" => "en",
            "value" => "en-GB",
        ];

        $companyRegistration = new LocaleField($fieldArray);
        $this->assertEquals("en-GB", $companyRegistration->value);
        $this->assertEquals("en", $companyRegistration->language);
        $this->assertEquals("GB", $companyRegistration->country);
        $this->assertEquals("GBP", $companyRegistration->currency);
    }

    public function testConstructorNoValues()
    {
        $fieldArray = [
            "confidence" => 0,
            "country" => null,
            "currency" => null,
            "language" => null,
            "value" => null,
        ];
        $classification = new LocaleField($fieldArray);
        $this->assertNull($classification->value);
        $this->assertNull($classification->language);
        $this->assertNull($classification->country);
        $this->assertNull($classification->currency);
    }
}
