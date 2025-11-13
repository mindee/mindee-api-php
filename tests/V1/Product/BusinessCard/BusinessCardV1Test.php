<?php

namespace V1\Product\BusinessCard;

use Mindee\Parsing\Common\Document;
use Mindee\Product\BusinessCard;
use PHPUnit\Framework\TestCase;

class BusinessCardV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = \TestingUtilities::getV1DataDir() . "/products/business_card/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(BusinessCard\BusinessCardV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(BusinessCard\BusinessCardV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->firstname->value);
        $this->assertNull($prediction->lastname->value);
        $this->assertNull($prediction->jobTitle->value);
        $this->assertNull($prediction->company->value);
        $this->assertNull($prediction->email->value);
        $this->assertNull($prediction->phoneNumber->value);
        $this->assertNull($prediction->mobileNumber->value);
        $this->assertNull($prediction->faxNumber->value);
        $this->assertNull($prediction->address->value);
        $this->assertNull($prediction->website->value);
        $this->assertEquals(0, count($prediction->socialMedia));
    }
}
