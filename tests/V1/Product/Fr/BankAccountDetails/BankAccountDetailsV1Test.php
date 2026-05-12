<?php

declare(strict_types=1);

namespace V1\Product\Fr\BankAccountDetails;

use Mindee\Product\Fr\BankAccountDetails;
use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\Fr\BankAccountDetails\BankAccountDetailsV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class BankAccountDetailsV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/bank_account_details/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(BankAccountDetailsV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(BankAccountDetailsV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->iban->value);
        self::assertNull($prediction->accountHolderName->value);
        self::assertNull($prediction->swift->value);
    }
}
