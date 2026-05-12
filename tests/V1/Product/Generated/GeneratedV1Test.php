<?php

declare(strict_types=1);

namespace V1\Product\Generated;

use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Parsing\Common\Page;
use Mindee\V1\Parsing\Generated\GeneratedListField;
use Mindee\V1\Parsing\Generated\GeneratedObjectField;
use Mindee\V1\Parsing\Standard\PositionField;
use Mindee\V1\Parsing\Standard\StringField;
use Mindee\V1\Product\Generated\GeneratedV1;
use Mindee\V1\Product\Generated\GeneratedV1Page;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class GeneratedV1Test extends TestCase
{
    protected Document $internationalIdV1CompleteDoc;
    protected Document $internationalIdV1EmptyDoc;
    protected Document $invoiceV4EmptyDoc;
    protected Document $invoiceV4CompleteDoc;
    protected Page $invoiceV4Page0;

    protected function setUp(): void
    {
        $this->internationalIdV1CompleteDoc = new Document(
            GeneratedV1::class,
            json_decode(
                file_get_contents(TestingUtilities::getV1DataDir() . "/products/generated/response_v1/complete_international_id_v1.json"),
                true
            )["document"]
        );

        $this->internationalIdV1EmptyDoc = new Document(
            GeneratedV1::class,
            json_decode(
                file_get_contents(TestingUtilities::getV1DataDir() . "/products/generated/response_v1/empty_international_id_v1.json"),
                true
            )["document"]
        );

        $this->invoiceV4EmptyDoc = new Document(
            GeneratedV1::class,
            json_decode(
                file_get_contents(TestingUtilities::getV1DataDir() . "/products/generated/response_v1/empty_invoice_v4.json"),
                true
            )["document"]
        );

        $this->invoiceV4CompleteDoc = new Document(
            GeneratedV1::class,
            json_decode(
                file_get_contents(TestingUtilities::getV1DataDir() . "/products/generated/response_v1/complete_invoice_v4.json"),
                true
            )["document"]
        );

        $jsonData = json_decode(
            file_get_contents(TestingUtilities::getV1DataDir() . "/products/generated/response_v1/complete_invoice_v4.json"),
            true
        );
        $this->invoiceV4Page0 = new Page(
            GeneratedV1Page::class,
            $jsonData["document"]["inference"]["pages"][0]
        );
    }

    public function testInternationalIdV1EmptyDoc(): void
    {
        $docStr = file_get_contents(TestingUtilities::getV1DataDir() . "/products/generated/response_v1/summary_empty_international_id_v1.rst");

        $fields = $this->internationalIdV1EmptyDoc->inference->prediction->fields;

        self::assertInstanceOf(StringField::class, $fields["document_type"]);
        self::assertNull($fields["document_type"]->value);

        self::assertInstanceOf(StringField::class, $fields["document_number"]);
        self::assertNull($fields["document_number"]->value);

        self::assertInstanceOf(StringField::class, $fields["country_of_issue"]);
        self::assertNull($fields["country_of_issue"]->value);

        self::assertInstanceOf(GeneratedListField::class, $fields["surnames"]);
        self::assertCount(0, $fields["surnames"]->values);

        self::assertInstanceOf(GeneratedListField::class, $fields["given_names"]);
        self::assertCount(0, $fields["given_names"]->values);

        self::assertInstanceOf(StringField::class, $fields["sex"]);
        self::assertNull($fields["sex"]->value);

        self::assertInstanceOf(StringField::class, $fields["birth_date"]);
        self::assertNull($fields["birth_date"]->value);

        self::assertInstanceOf(StringField::class, $fields["birth_place"]);
        self::assertNull($fields["birth_place"]->value);

        self::assertInstanceOf(StringField::class, $fields["nationality"]);
        self::assertNull($fields["nationality"]->value);

        self::assertInstanceOf(StringField::class, $fields["issue_date"]);
        self::assertNull($fields["issue_date"]->value);

        self::assertInstanceOf(StringField::class, $fields["expiry_date"]);
        self::assertNull($fields["expiry_date"]->value);

        self::assertInstanceOf(StringField::class, $fields["address"]);
        self::assertNull($fields["address"]->value);

        self::assertInstanceOf(StringField::class, $fields["mrz1"]);
        self::assertNull($fields["mrz1"]->value);

        self::assertInstanceOf(StringField::class, $fields["mrz2"]);
        self::assertNull($fields["mrz2"]->value);

        self::assertInstanceOf(StringField::class, $fields["mrz3"]);
        self::assertNull($fields["mrz3"]->value);

        self::assertSame((string) $this->internationalIdV1EmptyDoc, $docStr);
    }


    public function testInternationalIdV1CompleteDoc(): void
    {
        $docStr = file_get_contents(TestingUtilities::getV1DataDir() . "/products/generated/response_v1/summary_full_international_id_v1.rst");

        $fields = $this->internationalIdV1CompleteDoc->inference->prediction->fields;

        self::assertInstanceOf(StringField::class, $fields["document_type"]);
        self::assertSame($fields["document_type"]->value, "NATIONAL_ID_CARD");

        self::assertInstanceOf(StringField::class, $fields["document_number"]);
        self::assertSame($fields["document_number"]->value, "99999999R");

        self::assertInstanceOf(StringField::class, $fields["country_of_issue"]);
        self::assertSame($fields["country_of_issue"]->value, "ESP");

        self::assertInstanceOf(GeneratedListField::class, $fields["surnames"]);
        self::assertSame($fields["surnames"]->values[0]->value, "ESPAÑOLA");
        self::assertSame($fields["surnames"]->values[1]->value, "ESPAÑOLA");

        self::assertInstanceOf(GeneratedListField::class, $fields["given_names"]);
        self::assertSame($fields["given_names"]->values[0]->value, "CARMEN");

        self::assertInstanceOf(StringField::class, $fields["sex"]);
        self::assertSame($fields["sex"]->value, "F");

        self::assertInstanceOf(StringField::class, $fields["birth_date"]);
        self::assertSame($fields["birth_date"]->value, "1980-01-01");

        self::assertInstanceOf(StringField::class, $fields["birth_place"]);
        self::assertSame($fields["birth_place"]->value, "MADRID");

        self::assertInstanceOf(StringField::class, $fields["nationality"]);
        self::assertSame($fields["nationality"]->value, "ESP");

        self::assertInstanceOf(StringField::class, $fields["issue_date"]);
        self::assertSame($fields["issue_date"]->value, "2015-01-01");

        self::assertInstanceOf(StringField::class, $fields["expiry_date"]);
        self::assertSame($fields["expiry_date"]->value, "2025-01-01");

        self::assertInstanceOf(StringField::class, $fields["address"]);
        self::assertSame($fields["address"]->value, "AVDA DE MADRID S-N MADRID MADRID");

        self::assertInstanceOf(StringField::class, $fields["mrz1"]);
        self::assertSame($fields["mrz1"]->value, "IDESPBAA000589599999999R<<<<<<");

        self::assertInstanceOf(StringField::class, $fields["mrz2"]);
        self::assertSame($fields["mrz2"]->value, "8001014F2501017ESP<<<<<<<<<<<7");

        self::assertInstanceOf(StringField::class, $fields["mrz3"]);
        self::assertSame($fields["mrz3"]->value, "ESPANOLA<ESPANOLA<<CARMEN<<<<<<");

        self::assertSame((string) $this->internationalIdV1CompleteDoc, $docStr);
    }


    public function testInvoiceV4CompleteDoc(): void
    {
        $docStr = file_get_contents(TestingUtilities::getV1DataDir() . "/products/generated/response_v1/summary_full_invoice_v4.rst");

        $fields = $this->invoiceV4CompleteDoc->inference->prediction->fields;

        self::assertInstanceOf(StringField::class, $fields["customer_address"]);
        self::assertSame($fields["customer_address"]->value, "1954 Bloon Street West Toronto, ON, M6P 3K9 Canada");

        self::assertInstanceOf(GeneratedListField::class, $fields["customer_company_registrations"]);
        self::assertCount(0, $fields["customer_company_registrations"]->values);

        self::assertInstanceOf(StringField::class, $fields["customer_name"]);
        self::assertSame($fields["customer_name"]->value, "JIRO DOI");

        self::assertInstanceOf(StringField::class, $fields["date"]);
        self::assertSame($fields["date"]->value, "2020-02-17");

        self::assertInstanceOf(StringField::class, $fields["document_type"]);
        self::assertSame($fields["document_type"]->value, "INVOICE");

        self::assertInstanceOf(StringField::class, $fields["due_date"]);
        self::assertSame($fields["due_date"]->value, "2020-02-17");

        self::assertInstanceOf(StringField::class, $fields["invoice_number"]);
        self::assertSame($fields["invoice_number"]->value, "0042004801351");

        self::assertInstanceOf(GeneratedListField::class, $fields["line_items"]);
        self::assertInstanceOf(GeneratedObjectField::class, $fields["line_items"]->values[0]);
        self::assertSame($fields["line_items"]->values[0]->description, "S)BOIE 5X500 FEUILLES A4");
        self::assertNull($fields["line_items"]->values[0]->product_code);
        self::assertNull($fields["line_items"]->values[0]->quantity);
        self::assertSame($fields["line_items"]->values[6]->quantity, "1.0");
        self::assertNull($fields["line_items"]->values[0]->tax_amount);
        self::assertNull($fields["line_items"]->values[0]->tax_rate);
        self::assertSame($fields["line_items"]->values[0]->total_amount, "2.63");
        self::assertNull($fields["line_items"]->values[0]->unit_price);
        self::assertSame($fields["line_items"]->values[6]->unit_price, "65.0");

        self::assertInstanceOf(GeneratedObjectField::class, $fields["locale"]);
        self::assertSame($fields["locale"]->currency, "EUR");
        self::assertSame($fields["locale"]->language, "fr");

        self::assertInstanceOf(GeneratedListField::class, $fields["reference_numbers"]);
        self::assertSame($fields["reference_numbers"]->values[0]->value, "AD29094");

        self::assertInstanceOf(StringField::class, $fields["supplier_address"]);
        self::assertSame($fields["supplier_address"]->value, "156 University Ave, Toronto ON, Canada M5H 2H7");

        self::assertInstanceOf(GeneratedListField::class, $fields["supplier_company_registrations"]);
        self::assertCount(0, $fields["supplier_company_registrations"]->values);

        self::assertInstanceOf(StringField::class, $fields["supplier_name"]);
        self::assertSame($fields["supplier_name"]->value, "TURNPIKE DESIGNS CO.");

        self::assertInstanceOf(GeneratedListField::class, $fields["supplier_payment_details"]);
        self::assertSame($fields["supplier_payment_details"]->values[0]->iban, "FR7640254025476501124705368");

        self::assertInstanceOf(GeneratedListField::class, $fields["taxes"]);
        self::assertInstanceOf(PositionField::class, $fields["taxes"]->values[0]->polygon);
        self::assertSame(
            array_map(static fn($point) => [$point->getX(), $point->getY()], $fields["taxes"]->values[0]->polygon->value->getCoordinates()),
            [[0.292, 0.749], [0.543, 0.749], [0.543, 0.763], [0.292, 0.763]]
        );
        self::assertSame($fields["taxes"]->values[0]->rate, "20.0");
        self::assertSame($fields["taxes"]->values[0]->value, "97.98");

        self::assertInstanceOf(StringField::class, $fields["total_amount"]);
        self::assertSame($fields["total_amount"]->value, "587.95");

        self::assertInstanceOf(StringField::class, $fields["total_net"]);
        self::assertSame($fields["total_net"]->value, "489.97");

        self::assertSame((string) $this->invoiceV4CompleteDoc, $docStr);
    }

    public function testInvoiceV4Page0(): void
    {
        $docStr = file_get_contents(TestingUtilities::getV1DataDir() . "/products/generated/response_v1/summary_page0_invoice_v4.rst");

        self::assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["customer_address"]);
        self::assertNull($this->invoiceV4Page0->prediction->fields["customer_address"]->value);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["customer_company_registrations"]);
        self::assertCount(0, $this->invoiceV4Page0->prediction->fields["customer_company_registrations"]->values);

        self::assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["customer_name"]);
        self::assertNull($this->invoiceV4Page0->prediction->fields["customer_name"]->value);

        self::assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["date"]);
        self::assertSame("2020-02-17", $this->invoiceV4Page0->prediction->fields["date"]->value);

        self::assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["document_type"]);
        self::assertSame("INVOICE", $this->invoiceV4Page0->prediction->fields["document_type"]->value);

        self::assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["due_date"]);
        self::assertSame("2020-02-17", $this->invoiceV4Page0->prediction->fields["due_date"]->value);

        self::assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["invoice_number"]);
        self::assertSame("0042004801351", $this->invoiceV4Page0->prediction->fields["invoice_number"]->value);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["line_items"]);
        self::assertInstanceOf(GeneratedObjectField::class, $this->invoiceV4Page0->prediction->fields["line_items"]->values[0]);
        self::assertSame("S)BOIE 5X500 FEUILLES A4", $this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->description);
        self::assertNull($this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->product_code);
        self::assertNull($this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->quantity);
        self::assertNull($this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->tax_amount);
        self::assertNull($this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->tax_rate);
        self::assertSame("2.63", $this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->total_amount);
        self::assertNull($this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->unit_price);

        self::assertInstanceOf(GeneratedObjectField::class, $this->invoiceV4Page0->prediction->fields["locale"]);
        self::assertSame("EUR", $this->invoiceV4Page0->prediction->fields["locale"]->currency);
        self::assertSame("fr", $this->invoiceV4Page0->prediction->fields["locale"]->language);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["reference_numbers"]);
        self::assertCount(0, $this->invoiceV4Page0->prediction->fields["reference_numbers"]->values);

        self::assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["supplier_address"]);
        self::assertNull($this->invoiceV4Page0->prediction->fields["supplier_address"]->value);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["supplier_company_registrations"]);
        self::assertCount(0, $this->invoiceV4Page0->prediction->fields["supplier_company_registrations"]->values);

        self::assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["supplier_name"]);
        self::assertNull($this->invoiceV4Page0->prediction->fields["supplier_name"]->value);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["supplier_payment_details"]);
        self::assertSame("FR7640254025476501124705368", $this->invoiceV4Page0->prediction->fields["supplier_payment_details"]->values[0]->iban);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["taxes"]);
        self::assertInstanceOf(PositionField::class, $this->invoiceV4Page0->prediction->fields["taxes"]->values[0]->polygon);
        self::assertSame([[0.292, 0.749], [0.543, 0.749], [0.543, 0.763], [0.292, 0.763]], array_map(static fn($point) => [$point->getX(), $point->getY()], $this->invoiceV4Page0->prediction->fields["taxes"]->values[0]->polygon->value->getCoordinates()));

        self::assertSame("20.0", $this->invoiceV4Page0->prediction->fields["taxes"]->values[0]->rate);
        self::assertSame("97.98", $this->invoiceV4Page0->prediction->fields["taxes"]->values[0]->value);

        self::assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["total_amount"]);
        self::assertSame("587.95", $this->invoiceV4Page0->prediction->fields["total_amount"]->value);
        self::assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["total_net"]);
        self::assertSame("489.97", $this->invoiceV4Page0->prediction->fields["total_net"]->value);

        self::assertSame((string) $this->invoiceV4Page0, $docStr);
    }

    public function testInvoiceV4EmptyDoc(): void
    {
        $docStr = file_get_contents(TestingUtilities::getV1DataDir() . "/products/generated/response_v1/summary_empty_invoice_v4.rst");

        self::assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["customer_address"]);
        self::assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["customer_address"]->value);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["customer_company_registrations"]);
        self::assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["customer_company_registrations"]->values);

        self::assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["customer_name"]);
        self::assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["customer_name"]->value);

        self::assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["date"]);
        self::assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["date"]->value);

        self::assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["document_type"]);
        self::assertSame("INVOICE", $this->invoiceV4EmptyDoc->inference->prediction->fields["document_type"]->value);

        self::assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["due_date"]);
        self::assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["due_date"]->value);

        self::assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["invoice_number"]);
        self::assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["invoice_number"]->value);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["line_items"]);
        self::assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["line_items"]->values);

        self::assertInstanceOf(GeneratedObjectField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["locale"]);
        self::assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["locale"]->currency);
        self::assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["locale"]->language);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["reference_numbers"]);
        self::assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["reference_numbers"]->values);

        self::assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_address"]);
        self::assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_address"]->value);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_company_registrations"]);
        self::assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_company_registrations"]->values);

        self::assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_name"]);
        self::assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_name"]->value);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_payment_details"]);
        self::assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_payment_details"]->values);

        self::assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["taxes"]);
        self::assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["taxes"]->values);

        self::assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["total_amount"]);
        self::assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["total_amount"]->value);

        self::assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["total_net"]);
        self::assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["total_net"]->value);

        self::assertSame($docStr, (string) ($this->invoiceV4EmptyDoc));
    }
}
