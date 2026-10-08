<?php

declare(strict_types=1);

namespace V2\FileOperations;

use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\V2\FileOperations\Crop;
use Mindee\V2\Product\Crop\CropResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

use function BernardLedit\Image\decode;

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
        $extractedCrops = $cropOperation->extractMultipleCrops($doc->inference->result->crops);

        self::assertCount(1, $extractedCrops);

        self::assertSame(0, $extractedCrops[0]->pageId);
        self::assertSame(0, $extractedCrops[0]->elementId);

        self::assertSame([2822, 1572], decode($extractedCrops[0]->buffer)->size());
    }

    public function testProcessesMultiPageReceiptSplitCorrectly(): void
    {
        $inputSample = new PathInput($this->cropDataDir . '/multipage_sample.pdf');

        $localResponse = new LocalResponse($this->cropDataDir . '/crop_multiple.json');
        $doc = $localResponse->deserializeResponse(CropResponse::class);

        $cropOperation = new Crop($inputSample);
        $extractedCrops = $cropOperation->extractMultipleCrops($doc->inference->result->crops);

        self::assertCount(2, $extractedCrops);

        self::assertSame(0, $extractedCrops[0]->pageId);
        self::assertSame(0, $extractedCrops[0]->elementId);

        self::assertSame([156, 757], decode($extractedCrops[0]->buffer)->size());

        self::assertSame(0, $extractedCrops[1]->pageId);
        self::assertSame(1, $extractedCrops[1]->elementId);

        self::assertSame([188, 691], decode($extractedCrops[1]->buffer)->size());
    }
}
