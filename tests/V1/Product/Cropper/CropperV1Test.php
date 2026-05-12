<?php

declare(strict_types=1);

namespace V1\Product\Cropper;

use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Parsing\Common\Page;
use Mindee\V1\Product\Cropper\CropperV1;
use Mindee\V1\Product\Cropper\CropperV1Page;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class CropperV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private Page $completePage0;
    private string $completeDocReference;
    private string $completePage0Reference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/cropper/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(CropperV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(CropperV1::class, $emptyDocJSON["document"]);
        $this->completePage0 = new Page(CropperV1Page::class, $completeDocJSON["document"]["inference"]["pages"][0]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
        $this->completePage0Reference = file_get_contents($productDir . "summary_page0.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->pages[0]->prediction;
        self::assertCount(0, $prediction->cropping);
    }
    public function testCompletePage0(): void
    {
        self::assertSame(0, $this->completePage0->id);
        self::assertSame($this->completePage0Reference, (string) ($this->completePage0));
    }
}
