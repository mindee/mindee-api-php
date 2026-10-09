<?php

declare(strict_types=1);

namespace V1\Image;

use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\V1\Client;
use Mindee\V1\Image\ImageExtractor;
use Mindee\V1\Product\MultiReceiptsDetector\MultiReceiptsDetectorV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

use function sprintf;

class ImageExtractorTest extends TestCase
{
    private Client $dummyClient;

    protected function setUp(): void
    {
        $this->dummyClient = new Client("dummy-key");
    }

    public function testGivenAnImageShouldExtractPositionFields(): void
    {
        $image = new PathInput(
            TestingUtilities::getV1DataDir() . "/products/multi_receipts_detector/default_sample.jpg"
        );
        $response = $this->getMultiReceiptsDetectorPrediction("complete");
        $inference = $response->document->inference;

        $extractor = new ImageExtractor($image);
        self::assertSame(1, $extractor->pageCount);

        foreach ($inference->pages as $page) {
            $subImages = $extractor->extractImagesFromPage($page->prediction->receipts, $page->id);
            foreach ($subImages as $i => $extractedImage) {
                self::assertNotNull($extractedImage->image);
                $extractedImage->writeToFile(TestingUtilities::getRootDataDir() . "/output");

                $source = $extractedImage->asInputSource();
                self::assertSame(
                    sprintf("default_sample.jpg_page0-%d.jpg", $i),
                    $source->fileName
                );
            }
        }
    }

    private function getMultiReceiptsDetectorPrediction($name)
    {
        $fileName = TestingUtilities::getV1DataDir() . "/products/multi_receipts_detector/response_v1/{$name}.json";
        $localResponse = new LocalResponse($fileName);
        return $this->dummyClient->loadPrediction(MultiReceiptsDetectorV1::class, $localResponse);
    }

    public function testGivenAPdfShouldExtractPositionFields(): void
    {
        $imageInput = new PathInput(
            TestingUtilities::getV1DataDir() . "/products/multi_receipts_detector/multipage_sample.pdf"
        );
        $response = $this->getMultiReceiptsDetectorPrediction("multipage_sample");
        $inference = $response->document->inference;
        self::assertNotEmpty($imageInput->readContents()[1]);

        $extractor = new ImageExtractor($imageInput);
        self::assertSame(2, $extractor->pageCount);

        foreach ($inference->pages as $page) {
            $subImages = $extractor->extractImagesFromPage($page->prediction->receipts, $page->id);

            foreach ($subImages as $i => $extractedImage) {
                self::assertNotNull($extractedImage->image);
                $extractedImage->writeToFile(TestingUtilities::getRootDataDir() . "/output");

                $source = $extractedImage->asInputSource();
                self::assertSame(
                    sprintf("multipage_sample.pdf_page%d-%d.jpg", $page->id, $i),
                    $source->fileName
                );
            }
        }
    }


    protected function tearDown(): void
    {
        $filesToDelete = [
            TestingUtilities::getRootDataDir() . "/output/barcodes_1D_page-001_001.jpg",
            TestingUtilities::getRootDataDir() . "/output/barcodes_2D_page-001_001.jpg",
            TestingUtilities::getRootDataDir() . "/output/barcodes_2D_page-001_002.jpg",
            TestingUtilities::getRootDataDir() . "/output/multipage_sample_page-001_001.jpg",
            TestingUtilities::getRootDataDir() . "/output/multipage_sample_page-001_002.jpg",
            TestingUtilities::getRootDataDir() . "/output/multipage_sample_page-001_003.jpg",
            TestingUtilities::getRootDataDir() . "/output/multipage_sample_page-002_001.jpg",
            TestingUtilities::getRootDataDir() . "/output/multipage_sample_page-002_002.jpg",
            TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_001.jpg",
            TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_002.jpg",
            TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_003.jpg",
            TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_004.jpg",
            TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_005.jpg",
            TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_006.jpg",
        ];

        foreach ($filesToDelete as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
