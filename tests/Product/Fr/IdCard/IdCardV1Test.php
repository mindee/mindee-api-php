<?php

namespace Product\Fr\IdCard;

use Mindee\Product\Fr\IdCard;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class IdCardV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private Page $completePage0;
    private string $completeDocReference;
    private string $completePage0Reference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/idcard_fr/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(IdCard\IdCardV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(IdCard\IdCardV1::class, $emptyDocJSON["document"]);
        $this->completePage0 = new Page(IdCard\IdCardV1Page::class, $completeDocJSON["document"]["inference"]["pages"][0]);
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
        $this->assertNull($prediction->idNumber->value);
        $this->assertEquals(0, count($prediction->givenNames));
        $this->assertNull($prediction->surname->value);
        $this->assertNull($prediction->birthDate->value);
        $this->assertNull($prediction->birthPlace->value);
        $this->assertNull($prediction->expiryDate->value);
        $this->assertNull($prediction->authority->value);
        $this->assertNull($prediction->gender->value);
        $this->assertNull($prediction->mrz1->value);
        $this->assertNull($prediction->mrz2->value);
    }
    public function testCompletePage0()
    {
        $this->assertEquals(0, $this->completePage0->id);
        $this->assertEquals($this->completePage0Reference, strval($this->completePage0));
    }
}
