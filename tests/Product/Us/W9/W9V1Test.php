<?php

namespace Product\Us\W9;

use Mindee\Product\Us\W9;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class W9V1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private Page $completePage0;
    private string $completeDocReference;
    private string $completePage0Reference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/us_w9/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(W9\W9V1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(W9\W9V1::class, $emptyDocJSON["document"]);
        $this->completePage0 = new Page(W9\W9V1Page::class, $completeDocJSON["document"]["inference"]["pages"][0]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
        $this->completePage0Reference = file_get_contents($productDir . "summary_page0.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->pages[0]->prediction;
        $this->assertNull($prediction->name->value);
        $this->assertNull($prediction->ssn->value);
        $this->assertNull($prediction->address->value);
        $this->assertNull($prediction->cityStateZip->value);
        $this->assertNull($prediction->businessName->value);
        $this->assertNull($prediction->ein->value);
        $this->assertNull($prediction->taxClassification->value);
        $this->assertNull($prediction->taxClassificationOtherDetails->value);
        $this->assertNull($prediction->w9RevisionDate->value);
        $this->assertNull($prediction->signaturePosition->polygon);
        $this->assertNull($prediction->signatureDatePosition->polygon);
        $this->assertNull($prediction->taxClassificationLlc->value);
    }
    public function testCompletePage0()
    {
        $this->assertEquals(0, $this->completePage0->id);
        $this->assertEquals($this->completePage0Reference, strval($this->completePage0));
    }
}
