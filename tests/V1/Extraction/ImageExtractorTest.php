<?php

namespace V1\Extraction;

use Mindee\Client;
use Mindee\Extraction\ImageExtractor;
use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\Product\BarcodeReader\BarcodeReaderV1;
use Mindee\Product\MultiReceiptsDetector\MultiReceiptsDetectorV1;
use PHPUnit\Framework\TestCase;

class ImageExtractorTest extends TestCase
{

    private Client $dummyClient;

    protected function setUp(): void
    {
        $this->dummyClient = new Client("dummy-key");
    }

    public function testGivenAnImageShouldExtractPositionFields()
    {
        $image = new PathInput(
            \TestingUtilities::getV1DataDir() . "/products/multi_receipts_detector/default_sample.jpg"
        );
        $response = $this->getMultiReceiptsDetectorPrediction("complete");
        $inference = $response->document->inference;

        $extractor = new ImageExtractor($image);
        $this->assertEquals(1, $extractor->getPageCount());

        foreach ($inference->pages as $page) {
            $subImages = $extractor->extractImagesFromPage($page->prediction->receipts, $page->id);
            foreach ($subImages as $i => $extractedImage) {
                $this->assertNotNull($extractedImage->image);
                $extractedImage->writeToFile(\TestingUtilities::getRootDataDir() . "/output");

                $source = $extractedImage->asInputSource();
                $this->assertEquals(
                    sprintf("default_sample_page-001_%03d.jpg", $i + 1),
                    $source->fileName
                );
            }
        }
    }

    private function getMultiReceiptsDetectorPrediction($name)
    {
        $fileName = \TestingUtilities::getV1DataDir() . "/products/multi_receipts_detector/response_v1/{$name}.json";
        $localResponse = new LocalResponse($fileName);
        return $this->dummyClient->loadPrediction(MultiReceiptsDetectorV1::class, $localResponse);
    }

    public function testGivenAnImageShouldExtractValueFields()
    {
        $image = new PathInput(\TestingUtilities::getV1DataDir() . "/products/barcode_reader/default_sample.jpg");
        $response = $this->getBarcodeReaderPrediction("complete");
        $inference = $response->document->inference;

        $extractor = new ImageExtractor($image);
        $this->assertEquals(1, $extractor->getPageCount());

        foreach ($inference->pages as $page) {
            $codes1D = $extractor->extractImagesFromPage($page->prediction->codes1D, $page->id, "barcodes_1D.jpg");
            foreach ($codes1D as $i => $extractedImage) {
                $this->assertNotNull($extractedImage->image);
                $source = $extractedImage->asInputSource();
                $this->assertEquals(
                    sprintf("barcodes_1D_page-001_%03d.jpg", $i + 1),
                    $source->fileName
                );
                $extractedImage->writeToFile(\TestingUtilities::getRootDataDir() . "/output");
            }

            $codes2D = $extractor->extractImagesFromPage($page->prediction->codes2D, $page->id, "barcodes_2D.jpg");
            foreach ($codes2D as $extractedImage) {
                $this->assertNotNull($extractedImage->image);
                $extractedImage->writeToFile(\TestingUtilities::getRootDataDir() . "/output");
            }
        }
    }

    public function testGivenAPdfShouldExtractPositionFields()
    {
        $imageInput = new PathInput(
            \TestingUtilities::getV1DataDir() . "/products/multi_receipts_detector/multipage_sample.pdf"
        );
        $response = $this->getMultiReceiptsDetectorPrediction("multipage_sample");
        $inference = $response->document->inference;
        $this->assertNotEmpty($imageInput->readContents()[1]);

        $extractor = new ImageExtractor($imageInput);
        $this->assertEquals(2, $extractor->getPageCount());

        foreach ($inference->pages as $page) {
            $subImages = $extractor->extractImagesFromPage($page->prediction->receipts, $page->id);

            foreach ($subImages as $i => $extractedImage) {
                $this->assertNotNull($extractedImage->image);
                $extractedImage->writeToFile(\TestingUtilities::getRootDataDir() . "/output");

                $source = $extractedImage->asInputSource();
                $this->assertEquals(
                    sprintf("multipage_sample_page-%03d_%03d.jpg", $page->id + 1, $i + 1),
                    $source->fileName
                );
            }
        }
    }

    private function getBarcodeReaderPrediction($name)
    {
        $fileName = \TestingUtilities::getV1DataDir() . "/products/barcode_reader/response_v1/{$name}.json";
        $localResponse = new LocalResponse($fileName);
        return $this->dummyClient->loadPrediction(BarcodeReaderV1::class, $localResponse);
    }

    protected function tearDown(): void
    {
        $filesToDelete = [
            \TestingUtilities::getRootDataDir() . "/output/barcodes_1D_page-001_001.jpg",
            \TestingUtilities::getRootDataDir() . "/output/barcodes_2D_page-001_001.jpg",
            \TestingUtilities::getRootDataDir() . "/output/barcodes_2D_page-001_002.jpg",
            \TestingUtilities::getRootDataDir() . "/output/multipage_sample_page-001_001.jpg",
            \TestingUtilities::getRootDataDir() . "/output/multipage_sample_page-001_002.jpg",
            \TestingUtilities::getRootDataDir() . "/output/multipage_sample_page-001_003.jpg",
            \TestingUtilities::getRootDataDir() . "/output/multipage_sample_page-002_001.jpg",
            \TestingUtilities::getRootDataDir() . "/output/multipage_sample_page-002_002.jpg",
            \TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_001.jpg",
            \TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_002.jpg",
            \TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_003.jpg",
            \TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_004.jpg",
            \TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_005.jpg",
            \TestingUtilities::getRootDataDir() . "/output/default_sample_page-001_006.jpg",
        ];

        foreach ($filesToDelete as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
