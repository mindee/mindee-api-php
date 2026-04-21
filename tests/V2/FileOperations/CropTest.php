<?php


namespace V2\FileOperations;

use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\V2\FileOperations\Crop;
use Mindee\V2\Product\Crop\CropResponse;
use PHPUnit\Framework\TestCase;

class CropTest extends TestCase
{
    private string $cropDataDir;

    protected function setUp(): void
    {
        $this->cropDataDir = \TestingUtilities::getV2DataDir() . '/products/crop';
    }

    public function testProcessesSinglePageCropSplitCorrectly(): void
    {
        $inputSample = new PathInput($this->cropDataDir . '/default_sample.jpg');

        $localResponse = new LocalResponse($this->cropDataDir . '/crop_single.json');
        $doc = $localResponse->deserializeResponse(CropResponse::class);

        $cropOperation = new Crop($inputSample);
        $extractedCrops = $cropOperation->extractCrops($doc->inference->result->crops);

        $this->assertCount(1, $extractedCrops);

        $this->assertEquals(0, $extractedCrops[0]->pageId);
        $this->assertEquals(0, $extractedCrops[0]->elementId);

        $bitmap0 = $extractedCrops[0]->image;

        $this->assertEquals(2822, $bitmap0->width ?? clone $bitmap0->getWidth());
        $this->assertEquals(1572, $bitmap0->height ?? clone $bitmap0->getHeight());
    }

    public function testProcessesMultiPageReceiptSplitCorrectly(): void
    {
        $inputSample = new PathInput($this->cropDataDir . '/multipage_sample.pdf');

        $localResponse = new LocalResponse($this->cropDataDir . '/crop_multiple.json');
        $doc = $localResponse->deserializeResponse(CropResponse::class);

        $cropOperation = new Crop($inputSample);
        $extractedCrops = $cropOperation->extractCrops($doc->inference->result->crops);

        $this->assertCount(2, $extractedCrops);

        $this->assertEquals(0, $extractedCrops[0]->pageId);
        $this->assertEquals(0, $extractedCrops[0]->elementId);

        $bitmap0 = $extractedCrops[0]->image;
        $this->assertEquals(156, $bitmap0->width ?? $bitmap0->getWidth());
        $this->assertEquals(757, $bitmap0->height ?? $bitmap0->getHeight());

        $this->assertEquals(0, $extractedCrops[1]->pageId);
        $this->assertEquals(1, $extractedCrops[1]->elementId);

        $bitmap1 = $extractedCrops[1]->image;
        $this->assertEquals(188, $bitmap1->width ?? $bitmap1->getWidth());
        $this->assertEquals(691, $bitmap1->height ?? $bitmap1->getHeight());
    }
}
