<?php

namespace Product\Fr\BankAccountDetails;

use Mindee\Product\Fr\BankAccountDetails;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class BankAccountDetailsV2Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private Page $completePage0;
    private string $completeDocReference;
    private string $completePage0Reference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/bank_account_details/response_v2/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(BankAccountDetails\BankAccountDetailsV2::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(BankAccountDetails\BankAccountDetailsV2::class, $emptyDocJSON["document"]);
        $this->completePage0 = new Page(BankAccountDetails\BankAccountDetailsV2Document::class, $completeDocJSON["document"]["inference"]["pages"][0]);
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
        $this->assertNull($prediction->accountHoldersNames->value);
        $this->assertNull($prediction->bban->bbanBankCode);
        $this->assertNull($prediction->bban->bbanBranchCode);
        $this->assertNull($prediction->bban->bbanKey);
        $this->assertNull($prediction->bban->bbanNumber);
        $this->assertNull($prediction->iban->value);
        $this->assertNull($prediction->swiftCode->value);
    }

    public function testCompletePage0()
    {
        $this->assertEquals(0, $this->completePage0->id);
        $this->assertEquals($this->completePage0Reference, strval($this->completePage0));
    }

}
