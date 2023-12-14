<?php

namespace Product\Us\DriverLicense;

use Mindee\Product\Us\DriverLicense;
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
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/us_driver_license/response_v1/";
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
        $this->assertNull($prediction->state->value);
        $this->assertNull($prediction->driverLicenseId->value);
        $this->assertNull($prediction->expiryDate->value);
        $this->assertNull($prediction->issuedDate->value);
        $this->assertNull($prediction->lastName->value);
        $this->assertNull($prediction->firstName->value);
        $this->assertNull($prediction->address->value);
        $this->assertNull($prediction->dateOfBirth->value);
        $this->assertNull($prediction->restrictions->value);
        $this->assertNull($prediction->endorsements->value);
        $this->assertNull($prediction->dlClass->value);
        $this->assertNull($prediction->sex->value);
        $this->assertNull($prediction->height->value);
        $this->assertNull($prediction->weight->value);
        $this->assertNull($prediction->hairColor->value);
        $this->assertNull($prediction->eyeColor->value);
        $this->assertNull($prediction->ddNumber->value);
    }

    public function testCompletePage0()
    {
        $this->assertEquals(0, $this->completePage0->id);
        $this->assertEquals($this->completePage0Reference, strval($this->completePage0));
    }

}
