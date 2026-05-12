<?php

declare(strict_types=1);

namespace V1\Product\DriverLicense;

use Mindee\Product\DriverLicense;
use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\DriverLicense\DriverLicenseV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class DriverLicenseV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/driver_license/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(DriverLicenseV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(DriverLicenseV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->countryCode->value);
        self::assertNull($prediction->state->value);
        self::assertNull($prediction->id->value);
        self::assertNull($prediction->category->value);
        self::assertNull($prediction->lastName->value);
        self::assertNull($prediction->firstName->value);
        self::assertNull($prediction->dateOfBirth->value);
        self::assertNull($prediction->placeOfBirth->value);
        self::assertNull($prediction->expiryDate->value);
        self::assertNull($prediction->issuedDate->value);
        self::assertNull($prediction->issuingAuthority->value);
        self::assertNull($prediction->mrz->value);
        self::assertNull($prediction->ddNumber->value);
    }
}
