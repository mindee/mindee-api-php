<?php

declare(strict_types=1);

namespace V1\Product\BusinessCard;

use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\BusinessCard\BusinessCardV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class BusinessCardV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/business_card/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(BusinessCardV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(BusinessCardV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->firstname->value);
        self::assertNull($prediction->lastname->value);
        self::assertNull($prediction->jobTitle->value);
        self::assertNull($prediction->company->value);
        self::assertNull($prediction->email->value);
        self::assertNull($prediction->phoneNumber->value);
        self::assertNull($prediction->mobileNumber->value);
        self::assertNull($prediction->faxNumber->value);
        self::assertNull($prediction->address->value);
        self::assertNull($prediction->website->value);
        self::assertCount(0, $prediction->socialMedia);
    }
}
