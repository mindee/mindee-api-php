<?php

declare(strict_types=1);

namespace V1\Product\Us\BankCheck;

use Mindee\Product\Us\BankCheck;
use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Parsing\Common\Page;
use Mindee\V1\Product\Us\BankCheck\BankCheckV1;
use Mindee\V1\Product\Us\BankCheck\BankCheckV1Page;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class BankCheckV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private Page $completePage0;
    private string $completeDocReference;
    private string $completePage0Reference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/bank_check/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(BankCheckV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(BankCheckV1::class, $emptyDocJSON["document"]);
        $this->completePage0 = new Page(BankCheckV1Page::class, $completeDocJSON["document"]["inference"]["pages"][0]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
        $this->completePage0Reference = file_get_contents($productDir . "summary_page0.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->date->value);
        self::assertNull($prediction->amount->value);
        self::assertCount(0, $prediction->payees);
        self::assertNull($prediction->routingNumber->value);
        self::assertNull($prediction->accountNumber->value);
        self::assertNull($prediction->checkNumber->value);
    }
    public function testCompletePage0(): void
    {
        self::assertSame(0, $this->completePage0->id);
        self::assertSame($this->completePage0Reference, (string) ($this->completePage0));
    }
}
