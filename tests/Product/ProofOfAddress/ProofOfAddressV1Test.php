<?php

namespace Product\ProofOfAddress;

use Mindee\Product\ProofOfAddress;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class ProofOfAddressV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/proof_of_address/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(ProofOfAddress\ProofOfAddressV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(ProofOfAddress\ProofOfAddressV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->locale->value);
        $this->assertNull($prediction->issuerName->value);
        $this->assertEquals(0, count($prediction->issuerCompanyRegistration));
        $this->assertNull($prediction->issuerAddress->value);
        $this->assertNull($prediction->recipientName->value);
        $this->assertEquals(0, count($prediction->recipientCompanyRegistration));
        $this->assertNull($prediction->recipientAddress->value);
        $this->assertEquals(0, count($prediction->dates));
        $this->assertNull($prediction->date->value);
    }
}
