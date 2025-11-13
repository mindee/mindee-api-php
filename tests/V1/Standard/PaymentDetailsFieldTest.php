<?php

namespace V1\Standard;

use Mindee\Parsing\Standard\PaymentDetailsField;
use PHPUnit\Framework\TestCase;

class PaymentDetailsFieldTest extends TestCase
{
    public function testConstructor()
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
                    0.123
                ],
                [
                    0.861,
                    0.123
                ],
                [
                    0.861,
                    0.14
                ],
                [
                    0.666,
                    0.14
                ]
            ],
        ];

        $companyRegistration = new PaymentDetailsField($fieldArray);
        $this->assertEquals("FR7640254025476501124705368", $companyRegistration->iban);
        $this->assertEquals("211212121212", $companyRegistration->routingNumber);
        $this->assertEquals("CEPAFRPP", $companyRegistration->swift);
        $this->assertEquals("12345678910", $companyRegistration->accountNumber);
    }

    public function testConstructorNoValues()
    {
        $fieldArray = [
            "confidence" => 0,
            "iban" => null,
            "routing_number" => null,
            "swift" => null,
            "account_number" => null,
        ];
        $companyRegistration = new PaymentDetailsField($fieldArray);
        $this->assertNull($companyRegistration->iban);
        $this->assertNull($companyRegistration->routingNumber);
        $this->assertNull($companyRegistration->swift);
        $this->assertNull($companyRegistration->accountNumber);
    }
}
