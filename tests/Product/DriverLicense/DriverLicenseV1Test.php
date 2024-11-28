<?php

namespace Product\DriverLicense;

use Mindee\Product\DriverLicense;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class DriverLicenseV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/driver_license/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(DriverLicense\DriverLicenseV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(DriverLicense\DriverLicenseV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->countryCode->value);
        $this->assertNull($prediction->state->value);
        $this->assertNull($prediction->id->value);
        $this->assertNull($prediction->category->value);
        $this->assertNull($prediction->lastName->value);
        $this->assertNull($prediction->firstName->value);
        $this->assertNull($prediction->dateOfBirth->value);
        $this->assertNull($prediction->placeOfBirth->value);
        $this->assertNull($prediction->expiryDate->value);
        $this->assertNull($prediction->issuedDate->value);
        $this->assertNull($prediction->issuingAuthority->value);
        $this->assertNull($prediction->mrz->value);
        $this->assertNull($prediction->ddNumber->value);
    }
}
