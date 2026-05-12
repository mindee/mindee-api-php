<?php

declare(strict_types=1);

namespace V2\FileOperations;

use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\V2\FileOperations\Crop;
use Mindee\V2\Product\Crop\CropResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class CropTest extends TestCase
{
    private string $cropDataDir;

    protected function setUp(): void
    {
        $this->cropDataDir = TestingUtilities::getV2DataDir() . '/products/crop';
    }

    public function testProcessesSinglePageCropSplitCorrectly(): void
    {
        $inputSample = new PathInput($this->cropDataDir . '/default_sample.jpg');

        $localResponse = new LocalResponse($this->cropDataDir . '/crop_single.json');
        $doc = $localResponse->deserializeResponse(CropResponse::class);

        $cropOperation = new Crop($inputSample);
        $extractedCrops = $cropOperation->extractCrops($doc->inference->result->crops);

        self::assertCount(1, $extractedCrops);

        self::assertSame(0, $extractedCrops[0]->pageId);
        self::assertSame(0, $extractedCrops[0]->elementId);

        $bitmap0 = $extractedCrops[0]->image;

        self::assertSame(2822, $bitmap0->width ?? clone $bitmap0->getWidth());
        self::assertSame(1572, $bitmap0->height ?? clone $bitmap0->getHeight());
    }

    public function testProcessesMultiPageReceiptSplitCorrectly(): void
    {
        $inputSample = new PathInput($this->cropDataDir . '/multipage_sample.pdf');

        $localResponse = new LocalResponse($this->cropDataDir . '/crop_multiple.json');
        $doc = $localResponse->deserializeResponse(CropResponse::class);

        $cropOperation = new Crop($inputSample);
        $extractedCrops = $cropOperation->extractCrops($doc->inference->result->crops);

        self::assertCount(2, $extractedCrops);

        self::assertSame(0, $extractedCrops[0]->pageId);
        self::assertSame(0, $extractedCrops[0]->elementId);

        $bitmap0 = $extractedCrops[0]->image;
        self::assertSame(156, $bitmap0->width ?? $bitmap0->getWidth());
        self::assertSame(757, $bitmap0->height ?? $bitmap0->getHeight());

        self::assertSame(0, $extractedCrops[1]->pageId);
        self::assertSame(1, $extractedCrops[1]->elementId);

        $bitmap1 = $extractedCrops[1]->image;
        self::assertSame(188, $bitmap1->width ?? $bitmap1->getWidth());
        self::assertSame(691, $bitmap1->height ?? $bitmap1->getHeight());
    }
}
