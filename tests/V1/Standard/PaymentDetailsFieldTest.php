<?php

declare(strict_types=1);

namespace V1\Standard;

use Mindee\V1\Parsing\Standard\PaymentDetailsField;
use PHPUnit\Framework\TestCase;

class PaymentDetailsFieldTest extends TestCase
{
    public function testConstructor(): void
    {
        $fieldArray = [
            "account_number" => "12345678910",
            "confidence" => 0.94,
            "iban" => "FR7640254025476501124705368",
            "routing_number" => "211212121212",
            "swift" => "CEPAFRPP",
            "polygon" => [
                [
                    0.666,
                    0.123,
                ],
                [
                    0.861,
                    0.123,
                ],
                [
                    0.861,
                    0.14,
                ],
                [
                    0.666,
                    0.14,
                ],
            ],
        ];

        $companyRegistration = new PaymentDetailsField($fieldArray);
        self::assertSame("FR7640254025476501124705368", $companyRegistration->iban);
        self::assertSame("211212121212", $companyRegistration->routingNumber);
        self::assertSame("CEPAFRPP", $companyRegistration->swift);
        self::assertSame("12345678910", $companyRegistration->accountNumber);
    }

    public function testConstructorNoValues(): void
    {
        $fieldArray = [
            "confidence" => 0,
            "iban" => null,
            "routing_number" => null,
            "swift" => null,
            "account_number" => null,
        ];
        $companyRegistration = new PaymentDetailsField($fieldArray);
        self::assertNull($companyRegistration->iban);
        self::assertNull($companyRegistration->routingNumber);
        self::assertNull($companyRegistration->swift);
        self::assertNull($companyRegistration->accountNumber);
    }
}
