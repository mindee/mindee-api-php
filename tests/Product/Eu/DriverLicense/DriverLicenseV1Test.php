<?php

namespace Product\Eu\DriverLicense;

use Mindee\Product\Eu\DriverLicense;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class DriverLicenseV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private Page $completePage0;
    private string $completeDocReference;
    private string $completePage0Reference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/eu_driver_license/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(DriverLicense\DriverLicenseV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(DriverLicense\DriverLicenseV1::class, $emptyDocJSON["document"]);
        $this->completePage0 = new Page(DriverLicense\DriverLicenseV1Page::class, $completeDocJSON["document"]["inference"]["pages"][0]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
        $this->completePage0Reference = file_get_contents($productDir . "summary_page0.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->countryCode->value);
        $this->assertNull($prediction->documentId->value);
        $this->assertNull($prediction->category->value);
        $this->assertNull($prediction->lastName->value);
        $this->assertNull($prediction->firstName->value);
        $this->assertNull($prediction->dateOfBirth->value);
        $this->assertNull($prediction->placeOfBirth->value);
        $this->assertNull($prediction->expiryDate->value);
        $this->assertNull($prediction->issueDate->value);
        $this->assertNull($prediction->issueAuthority->value);
        $this->assertNull($prediction->mrz->value);
        $this->assertNull($prediction->address->value);
    }
    public function testCompletePage0()
    {
        $this->assertEquals(0, $this->completePage0->id);
        $this->assertEquals($this->completePage0Reference, strval($this->completePage0));
    }
}
