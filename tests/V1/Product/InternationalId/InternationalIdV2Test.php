<?php

declare(strict_types=1);

namespace V1\Product\InternationalId;

use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\InternationalId\InternationalIdV2;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class InternationalIdV2Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/international_id/response_v2/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(InternationalIdV2::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(InternationalIdV2::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->documentNumber->value);
        self::assertCount(0, $prediction->surnames);
        self::assertCount(0, $prediction->givenNames);
        self::assertNull($prediction->sex->value);
        self::assertNull($prediction->birthDate->value);
        self::assertNull($prediction->birthPlace->value);
        self::assertNull($prediction->nationality->value);
        self::assertNull($prediction->personalNumber->value);
        self::assertNull($prediction->countryOfIssue->value);
        self::assertNull($prediction->stateOfIssue->value);
        self::assertNull($prediction->issueDate->value);
        self::assertNull($prediction->expiryDate->value);
        self::assertNull($prediction->address->value);
        self::assertNull($prediction->mrzLine1->value);
        self::assertNull($prediction->mrzLine2->value);
        self::assertNull($prediction->mrzLine3->value);
    }
}
