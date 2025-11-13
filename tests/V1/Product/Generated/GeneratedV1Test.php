<?php

namespace V1\Product\Generated;

use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use Mindee\Parsing\Generated\GeneratedListField;
use Mindee\Parsing\Generated\GeneratedObjectField;
use Mindee\Parsing\Standard\PositionField;
use Mindee\Parsing\Standard\StringField;
use Mindee\Product\Generated\GeneratedV1;
use Mindee\Product\Generated\GeneratedV1Page;
use PHPUnit\Framework\TestCase;


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
                file_get_contents(\TestingUtilities::getV1DataDir() . "/products/generated/response_v1/complete_international_id_v1.json"),
                true
            )["document"]
        );

        $this->internationalIdV1EmptyDoc = new Document(
            GeneratedV1::class,
            json_decode(
                file_get_contents(\TestingUtilities::getV1DataDir() . "/products/generated/response_v1/empty_international_id_v1.json"),
                true
            )["document"]
        );

        $this->invoiceV4EmptyDoc = new Document(
            GeneratedV1::class,
            json_decode(
                file_get_contents(\TestingUtilities::getV1DataDir() . "/products/generated/response_v1/empty_invoice_v4.json"),
                true
            )["document"]
        );

        $this->invoiceV4CompleteDoc = new Document(
            GeneratedV1::class,
            json_decode(
                file_get_contents(\TestingUtilities::getV1DataDir() . "/products/generated/response_v1/complete_invoice_v4.json"),
                true
            )["document"]
        );

        $jsonData = json_decode(
            file_get_contents(\TestingUtilities::getV1DataDir() . "/products/generated/response_v1/complete_invoice_v4.json"),
            true
        );
        $this->invoiceV4Page0 = new Page(
            GeneratedV1Page::class,
            $jsonData["document"]["inference"]["pages"][0]
        );
    }

    public function testInternationalIdV1EmptyDoc(): void
    {
        $docStr = file_get_contents(\TestingUtilities::getV1DataDir() . "/products/generated/response_v1/summary_empty_international_id_v1.rst");

        $fields = $this->internationalIdV1EmptyDoc->inference->prediction->fields;

        $this->assertInstanceOf(StringField::class, $fields["document_type"]);
        $this->assertNull($fields["document_type"]->value);

        $this->assertInstanceOf(StringField::class, $fields["document_number"]);
        $this->assertNull($fields["document_number"]->value);

        $this->assertInstanceOf(StringField::class, $fields["country_of_issue"]);
        $this->assertNull($fields["country_of_issue"]->value);

        $this->assertInstanceOf(GeneratedListField::class, $fields["surnames"]);
        $this->assertCount(0, $fields["surnames"]->values);

        $this->assertInstanceOf(GeneratedListField::class, $fields["given_names"]);
        $this->assertCount(0, $fields["given_names"]->values);

        $this->assertInstanceOf(StringField::class, $fields["sex"]);
        $this->assertNull($fields["sex"]->value);

        $this->assertInstanceOf(StringField::class, $fields["birth_date"]);
        $this->assertNull($fields["birth_date"]->value);

        $this->assertInstanceOf(StringField::class, $fields["birth_place"]);
        $this->assertNull($fields["birth_place"]->value);

        $this->assertInstanceOf(StringField::class, $fields["nationality"]);
        $this->assertNull($fields["nationality"]->value);

        $this->assertInstanceOf(StringField::class, $fields["issue_date"]);
        $this->assertNull($fields["issue_date"]->value);

        $this->assertInstanceOf(StringField::class, $fields["expiry_date"]);
        $this->assertNull($fields["expiry_date"]->value);

        $this->assertInstanceOf(StringField::class, $fields["address"]);
        $this->assertNull($fields["address"]->value);

        $this->assertInstanceOf(StringField::class, $fields["mrz1"]);
        $this->assertNull($fields["mrz1"]->value);

        $this->assertInstanceOf(StringField::class, $fields["mrz2"]);
        $this->assertNull($fields["mrz2"]->value);

        $this->assertInstanceOf(StringField::class, $fields["mrz3"]);
        $this->assertNull($fields["mrz3"]->value);

        $this->assertSame((string)$this->internationalIdV1EmptyDoc, $docStr);
    }


    public function testInternationalIdV1CompleteDoc(): void
    {
        $docStr = file_get_contents(\TestingUtilities::getV1DataDir() . "/products/generated/response_v1/summary_full_international_id_v1.rst");

        $fields = $this->internationalIdV1CompleteDoc->inference->prediction->fields;

        $this->assertInstanceOf(StringField::class, $fields["document_type"]);
        $this->assertSame($fields["document_type"]->value, "NATIONAL_ID_CARD");

        $this->assertInstanceOf(StringField::class, $fields["document_number"]);
        $this->assertSame($fields["document_number"]->value, "99999999R");

        $this->assertInstanceOf(StringField::class, $fields["country_of_issue"]);
        $this->assertSame($fields["country_of_issue"]->value, "ESP");

        $this->assertInstanceOf(GeneratedListField::class, $fields["surnames"]);
        $this->assertSame($fields["surnames"]->values[0]->value, "ESPAÑOLA");
        $this->assertSame($fields["surnames"]->values[1]->value, "ESPAÑOLA");

        $this->assertInstanceOf(GeneratedListField::class, $fields["given_names"]);
        $this->assertSame($fields["given_names"]->values[0]->value, "CARMEN");

        $this->assertInstanceOf(StringField::class, $fields["sex"]);
        $this->assertSame($fields["sex"]->value, "F");

        $this->assertInstanceOf(StringField::class, $fields["birth_date"]);
        $this->assertSame($fields["birth_date"]->value, "1980-01-01");

        $this->assertInstanceOf(StringField::class, $fields["birth_place"]);
        $this->assertSame($fields["birth_place"]->value, "MADRID");

        $this->assertInstanceOf(StringField::class, $fields["nationality"]);
        $this->assertSame($fields["nationality"]->value, "ESP");

        $this->assertInstanceOf(StringField::class, $fields["issue_date"]);
        $this->assertSame($fields["issue_date"]->value, "2015-01-01");

        $this->assertInstanceOf(StringField::class, $fields["expiry_date"]);
        $this->assertSame($fields["expiry_date"]->value, "2025-01-01");

        $this->assertInstanceOf(StringField::class, $fields["address"]);
        $this->assertSame($fields["address"]->value, "AVDA DE MADRID S-N MADRID MADRID");

        $this->assertInstanceOf(StringField::class, $fields["mrz1"]);
        $this->assertSame($fields["mrz1"]->value, "IDESPBAA000589599999999R<<<<<<");

        $this->assertInstanceOf(StringField::class, $fields["mrz2"]);
        $this->assertSame($fields["mrz2"]->value, "8001014F2501017ESP<<<<<<<<<<<7");

        $this->assertInstanceOf(StringField::class, $fields["mrz3"]);
        $this->assertSame($fields["mrz3"]->value, "ESPANOLA<ESPANOLA<<CARMEN<<<<<<");

        $this->assertSame((string)$this->internationalIdV1CompleteDoc, $docStr);
    }


    public function testInvoiceV4CompleteDoc(): void
    {
        $docStr = file_get_contents(\TestingUtilities::getV1DataDir() . "/products/generated/response_v1/summary_full_invoice_v4.rst");

        $fields = $this->invoiceV4CompleteDoc->inference->prediction->fields;

        $this->assertInstanceOf(StringField::class, $fields["customer_address"]);
        $this->assertSame($fields["customer_address"]->value, "1954 Bloon Street West Toronto, ON, M6P 3K9 Canada");

        $this->assertInstanceOf(GeneratedListField::class, $fields["customer_company_registrations"]);
        $this->assertCount(0, $fields["customer_company_registrations"]->values);

        $this->assertInstanceOf(StringField::class, $fields["customer_name"]);
        $this->assertSame($fields["customer_name"]->value, "JIRO DOI");

        $this->assertInstanceOf(StringField::class, $fields["date"]);
        $this->assertSame($fields["date"]->value, "2020-02-17");

        $this->assertInstanceOf(StringField::class, $fields["document_type"]);
        $this->assertSame($fields["document_type"]->value, "INVOICE");

        $this->assertInstanceOf(StringField::class, $fields["due_date"]);
        $this->assertSame($fields["due_date"]->value, "2020-02-17");

        $this->assertInstanceOf(StringField::class, $fields["invoice_number"]);
        $this->assertSame($fields["invoice_number"]->value, "0042004801351");

        $this->assertInstanceOf(GeneratedListField::class, $fields["line_items"]);
        $this->assertInstanceOf(GeneratedObjectField::class, $fields["line_items"]->values[0]);
        $this->assertSame($fields["line_items"]->values[0]->description, "S)BOIE 5X500 FEUILLES A4");
        $this->assertNull($fields["line_items"]->values[0]->product_code);
        $this->assertNull($fields["line_items"]->values[0]->quantity);
        $this->assertSame($fields["line_items"]->values[6]->quantity, "1.0");
        $this->assertNull($fields["line_items"]->values[0]->tax_amount);
        $this->assertNull($fields["line_items"]->values[0]->tax_rate);
        $this->assertSame($fields["line_items"]->values[0]->total_amount, "2.63");
        $this->assertNull($fields["line_items"]->values[0]->unit_price);
        $this->assertSame($fields["line_items"]->values[6]->unit_price, "65.0");

        $this->assertInstanceOf(GeneratedObjectField::class, $fields["locale"]);
        $this->assertSame($fields["locale"]->currency, "EUR");
        $this->assertSame($fields["locale"]->language, "fr");

        $this->assertInstanceOf(GeneratedListField::class, $fields["reference_numbers"]);
        $this->assertSame($fields["reference_numbers"]->values[0]->value, "AD29094");

        $this->assertInstanceOf(StringField::class, $fields["supplier_address"]);
        $this->assertSame($fields["supplier_address"]->value, "156 University Ave, Toronto ON, Canada M5H 2H7");

        $this->assertInstanceOf(GeneratedListField::class, $fields["supplier_company_registrations"]);
        $this->assertCount(0, $fields["supplier_company_registrations"]->values);

        $this->assertInstanceOf(StringField::class, $fields["supplier_name"]);
        $this->assertSame($fields["supplier_name"]->value, "TURNPIKE DESIGNS CO.");

        $this->assertInstanceOf(GeneratedListField::class, $fields["supplier_payment_details"]);
        $this->assertSame($fields["supplier_payment_details"]->values[0]->iban, "FR7640254025476501124705368");

        $this->assertInstanceOf(GeneratedListField::class, $fields["taxes"]);
        $this->assertInstanceOf(PositionField::class, $fields["taxes"]->values[0]->polygon);
        $this->assertSame(
            array_map(function ($point) {
                return [$point->getX(), $point->getY()];
            }, $fields["taxes"]->values[0]->polygon->value->getCoordinates()),
            [[0.292, 0.749], [0.543, 0.749], [0.543, 0.763], [0.292, 0.763]]
        );
        $this->assertSame($fields["taxes"]->values[0]->rate, "20.0");
        $this->assertSame($fields["taxes"]->values[0]->value, "97.98");

        $this->assertInstanceOf(StringField::class, $fields["total_amount"]);
        $this->assertSame($fields["total_amount"]->value, "587.95");

        $this->assertInstanceOf(StringField::class, $fields["total_net"]);
        $this->assertSame($fields["total_net"]->value, "489.97");

        $this->assertSame((string)$this->invoiceV4CompleteDoc, $docStr);
    }

    public function testInvoiceV4Page0()
    {
        $docStr = file_get_contents(\TestingUtilities::getV1DataDir() . "/products/generated/response_v1/summary_page0_invoice_v4.rst");

        $this->assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["customer_address"]);
        $this->assertNull($this->invoiceV4Page0->prediction->fields["customer_address"]->value);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["customer_company_registrations"]);
        $this->assertCount(0, $this->invoiceV4Page0->prediction->fields["customer_company_registrations"]->values);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["customer_name"]);
        $this->assertNull($this->invoiceV4Page0->prediction->fields["customer_name"]->value);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["date"]);
        $this->assertEquals("2020-02-17", $this->invoiceV4Page0->prediction->fields["date"]->value);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["document_type"]);
        $this->assertEquals("INVOICE", $this->invoiceV4Page0->prediction->fields["document_type"]->value);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["due_date"]);
        $this->assertEquals("2020-02-17", $this->invoiceV4Page0->prediction->fields["due_date"]->value);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["invoice_number"]);
        $this->assertEquals("0042004801351", $this->invoiceV4Page0->prediction->fields["invoice_number"]->value);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["line_items"]);
        $this->assertInstanceOf(GeneratedObjectField::class, $this->invoiceV4Page0->prediction->fields["line_items"]->values[0]);
        $this->assertEquals("S)BOIE 5X500 FEUILLES A4", $this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->description);
        $this->assertNull($this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->product_code);
        $this->assertNull($this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->quantity);
        $this->assertNull($this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->tax_amount);
        $this->assertNull($this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->tax_rate);
        $this->assertEquals("2.63", $this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->total_amount);
        $this->assertNull($this->invoiceV4Page0->prediction->fields["line_items"]->values[0]->unit_price);

        $this->assertInstanceOf(GeneratedObjectField::class, $this->invoiceV4Page0->prediction->fields["locale"]);
        $this->assertEquals("EUR", $this->invoiceV4Page0->prediction->fields["locale"]->currency);
        $this->assertEquals("fr", $this->invoiceV4Page0->prediction->fields["locale"]->language);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["reference_numbers"]);
        $this->assertCount(0, $this->invoiceV4Page0->prediction->fields["reference_numbers"]->values);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["supplier_address"]);
        $this->assertNull($this->invoiceV4Page0->prediction->fields["supplier_address"]->value);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["supplier_company_registrations"]);
        $this->assertCount(0, $this->invoiceV4Page0->prediction->fields["supplier_company_registrations"]->values);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["supplier_name"]);
        $this->assertNull($this->invoiceV4Page0->prediction->fields["supplier_name"]->value);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["supplier_payment_details"]);
        $this->assertEquals("FR7640254025476501124705368", $this->invoiceV4Page0->prediction->fields["supplier_payment_details"]->values[0]->iban);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4Page0->prediction->fields["taxes"]);
        $this->assertInstanceOf(PositionField::class, $this->invoiceV4Page0->prediction->fields["taxes"]->values[0]->polygon);
        $this->assertEquals([[0.292, 0.749], [0.543, 0.749], [0.543, 0.763], [0.292, 0.763]], array_map(function ($point) {
            return [$point->getX(), $point->getY()];
        }, $this->invoiceV4Page0->prediction->fields["taxes"]->values[0]->polygon->value->getCoordinates()));

        $this->assertEquals("20.0", $this->invoiceV4Page0->prediction->fields["taxes"]->values[0]->rate);
        $this->assertEquals("97.98", $this->invoiceV4Page0->prediction->fields["taxes"]->values[0]->value);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["total_amount"]);
        $this->assertEquals("587.95", $this->invoiceV4Page0->prediction->fields["total_amount"]->value);
        $this->assertInstanceOf(StringField::class, $this->invoiceV4Page0->prediction->fields["total_net"]);
        $this->assertEquals("489.97", $this->invoiceV4Page0->prediction->fields["total_net"]->value);

        $this->assertSame((string)$this->invoiceV4Page0, $docStr);
    }

    public function testInvoiceV4EmptyDoc()
    {
        $docStr = file_get_contents(\TestingUtilities::getV1DataDir() . "/products/generated/response_v1/summary_empty_invoice_v4.rst");

        $this->assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["customer_address"]);
        $this->assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["customer_address"]->value);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["customer_company_registrations"]);
        $this->assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["customer_company_registrations"]->values);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["customer_name"]);
        $this->assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["customer_name"]->value);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["date"]);
        $this->assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["date"]->value);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["document_type"]);
        $this->assertEquals("INVOICE", $this->invoiceV4EmptyDoc->inference->prediction->fields["document_type"]->value);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["due_date"]);
        $this->assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["due_date"]->value);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["invoice_number"]);
        $this->assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["invoice_number"]->value);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["line_items"]);
        $this->assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["line_items"]->values);

        $this->assertInstanceOf(GeneratedObjectField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["locale"]);
        $this->assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["locale"]->currency);
        $this->assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["locale"]->language);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["reference_numbers"]);
        $this->assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["reference_numbers"]->values);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_address"]);
        $this->assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_address"]->value);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_company_registrations"]);
        $this->assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_company_registrations"]->values);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_name"]);
        $this->assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_name"]->value);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_payment_details"]);
        $this->assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["supplier_payment_details"]->values);

        $this->assertInstanceOf(GeneratedListField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["taxes"]);
        $this->assertCount(0, $this->invoiceV4EmptyDoc->inference->prediction->fields["taxes"]->values);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["total_amount"]);
        $this->assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["total_amount"]->value);

        $this->assertInstanceOf(StringField::class, $this->invoiceV4EmptyDoc->inference->prediction->fields["total_net"]);
        $this->assertNull($this->invoiceV4EmptyDoc->inference->prediction->fields["total_net"]->value);

        $this->assertEquals($docStr, strval($this->invoiceV4EmptyDoc));
    }
}
