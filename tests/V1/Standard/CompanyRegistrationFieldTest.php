<?php

namespace V1\Standard;

use Mindee\Parsing\Standard\CompanyRegistrationField;
use PHPUnit\Framework\TestCase;

class CompanyRegistrationFieldTest extends TestCase
{
    public function testConstructor()
    {
        $fieldArray = [
            [
                "confidence" => 1.0,
                "polygon" => [
                    [346, 0.199],
                    [0.484, 0.199],
                    [0.484, 0.217],
                    [0.346, 0.21]
                ]
            ],
            "type" => "VAT NUMBER",
            "value" => "FR00000000000"
        ];

        $companyRegistration = new CompanyRegistrationField($fieldArray);
        $this->assertEquals("FR00000000000", $companyRegistration->value);
        $this->assertEquals("VAT NUMBER", $companyRegistration->type);
    }
}
