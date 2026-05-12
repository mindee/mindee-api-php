<?php

declare(strict_types=1);

namespace V1\Standard;

use Mindee\V1\Parsing\Standard\LocaleField;
use PHPUnit\Framework\TestCase;

class LocaleFieldTest extends TestCase
{
    public function testConstructor(): void
    {
        $fieldArray = [
            "confidence" => 0.82,
            "country" => "GB",
            "currency" => "GBP",
            "language" => "en",
            "value" => "en-GB",
        ];

        $companyRegistration = new LocaleField($fieldArray);
        self::assertSame("en-GB", $companyRegistration->value);
        self::assertSame("en", $companyRegistration->language);
        self::assertSame("GB", $companyRegistration->country);
        self::assertSame("GBP", $companyRegistration->currency);
    }

    public function testConstructorNoValues(): void
    {
        $fieldArray = [
            "confidence" => 0,
            "country" => null,
            "currency" => null,
            "language" => null,
            "value" => null,
        ];
        $classification = new LocaleField($fieldArray);
        self::assertNull($classification->value);
        self::assertNull($classification->language);
        self::assertNull($classification->country);
        self::assertNull($classification->currency);
    }
}
