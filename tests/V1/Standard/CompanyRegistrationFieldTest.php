<?php

declare(strict_types=1);

namespace V1\Standard;

use Mindee\V1\Parsing\Standard\CompanyRegistrationField;
use PHPUnit\Framework\TestCase;

class CompanyRegistrationFieldTest extends TestCase
{
    public function testConstructor(): void
    {
        $fieldArray = [
            [
                "confidence" => 1.0,
                "polygon" => [
                    [346, 0.199],
                    [0.484, 0.199],
                    [0.484, 0.217],
                    [0.346, 0.21],
                ],
            ],
            "type" => "VAT NUMBER",
            "value" => "FR00000000000",
        ];

        $companyRegistration = new CompanyRegistrationField($fieldArray);
        self::assertSame("FR00000000000", $companyRegistration->value);
        self::assertSame("VAT NUMBER", $companyRegistration->type);
    }
}
