<?php

declare(strict_types=1);

namespace V1\Product\Passport;

use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\Passport\PassportV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class PassportV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/passport/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(PassportV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(PassportV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->country->value);
        self::assertNull($prediction->idNumber->value);
        self::assertCount(0, $prediction->givenNames);
        self::assertNull($prediction->surname->value);
        self::assertNull($prediction->birthDate->value);
        self::assertNull($prediction->birthPlace->value);
        self::assertNull($prediction->gender->value);
        self::assertNull($prediction->issuanceDate->value);
        self::assertNull($prediction->expiryDate->value);
        self::assertNull($prediction->mrz1->value);
        self::assertNull($prediction->mrz2->value);
    }
}
