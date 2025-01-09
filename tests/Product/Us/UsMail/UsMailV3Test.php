<?php

namespace Product\Us\UsMail;

use Mindee\Product\Us\UsMail;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class UsMailV3Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/us_mail/response_v3/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(UsMail\UsMailV3::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(UsMail\UsMailV3::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->senderName->value);
        $this->assertNull($prediction->senderAddress->city);
        $this->assertNull($prediction->senderAddress->complete);
        $this->assertNull($prediction->senderAddress->postalCode);
        $this->assertNull($prediction->senderAddress->state);
        $this->assertNull($prediction->senderAddress->street);
        $this->assertEquals(0, count($prediction->recipientNames));
        $this->assertEquals(0, count($prediction->recipientAddresses));
        $this->assertNull($prediction->isReturnToSender->value);
    }
}
