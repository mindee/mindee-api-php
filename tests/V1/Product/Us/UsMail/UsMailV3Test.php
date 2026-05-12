<?php

declare(strict_types=1);

namespace V1\Product\Us\UsMail;

use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\Us\UsMail\UsMailV3;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class UsMailV3Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/us_mail/response_v3/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(UsMailV3::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(UsMailV3::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->senderName->value);
        self::assertNull($prediction->senderAddress->city);
        self::assertNull($prediction->senderAddress->complete);
        self::assertNull($prediction->senderAddress->postalCode);
        self::assertNull($prediction->senderAddress->state);
        self::assertNull($prediction->senderAddress->street);
        self::assertCount(0, $prediction->recipientNames);
        self::assertCount(0, $prediction->recipientAddresses);
        self::assertNull($prediction->isReturnToSender->value);
    }
}
