<?php

declare(strict_types=1);

namespace V2\FileOperations;

use Mindee\Input\PathInput;
use Mindee\V2\Client;
use Mindee\V2\FileOperations\Crop;
use Mindee\V2\Product\Crop\CropResponse;
use Mindee\V2\Product\Crop\Params\CropParameters;
use Mindee\V2\Product\Extraction\ExtractionResponse;
use Mindee\V2\Product\Extraction\Params\ExtractionParameters;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

use function strlen;

class CropFunctional extends TestCase
{
    private Client $client;
    private string $cropModelId;
    private string $findocModelId;
    private string $outputDir;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY') ?: '';
        $this->client = new Client($apiKey);
        $this->cropModelId = getenv('MINDEE_V2_CROP_MODEL_ID') ?: '';
        $this->findocModelId = getenv('MINDEE_V2_FINDOC_MODEL_ID') ?: '';

        $this->outputDir = getcwd() . '/output';
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0o777, true);
        }
    }

    protected function tearDown(): void
    {
        $file1 = $this->outputDir . '/crop_001.jpg';
        $file2 = $this->outputDir . '/crop_002.jpg';

        if (file_exists($file1)) {
            unlink($file1);
        }
        if (file_exists($file2)) {
            unlink($file2);
        }
    }

    private function checkFindocReturn(ExtractionResponse $findocResponse): void
    {
        self::assertGreaterThan(0, strlen($findocResponse->inference->model->id));

        $totalAmount = $findocResponse->inference->result->fields['total_amount'];
        self::assertNotNull($totalAmount);
        self::assertGreaterThan(0, $totalAmount->value);
    }

    public function testExtractCropsFromImageCorrectly(): void
    {
        $inputSource = new PathInput(TestingUtilities::getV2ProductDir() . '/crop/default_sample.jpg');
        $cropParams = new CropParameters($this->cropModelId);

        $response = $this->client->enqueueAndGetResult(CropResponse::class, $inputSource, $cropParams);

        self::assertNotNull($response);
        self::assertCount(2, $response->inference->result->crops);
        self::assertInstanceOf(CropResponse::class, $response);
        $extractedImages = $response->inference->result->extractFromInputSource($inputSource);
        $extractedImage0 = $response->inference->result->crops[0]->extractFromInputSource($inputSource);
        self::assertEquals($extractedImage0, $extractedImages[0]);

        self::assertCount(2, $extractedImages);
        self::assertSame('default_sample.jpg_page0-0.jpg', $extractedImages[0]->filename);
        self::assertSame('default_sample.jpg_page0-1.jpg', $extractedImages[1]->filename);

        $extractionInput = $extractedImages[0]->asInputSource();
        $findocParams = new ExtractionParameters($this->findocModelId);

        $invoice0 = $this->client->enqueueAndGetResult(ExtractionResponse::class, $extractionInput, $findocParams);

        $this->checkFindocReturn($invoice0);

        $extractedImages->saveAllToDisk($this->outputDir, quality: 50);

        $file1Info = filesize($this->outputDir . '/crop_001.jpg');
        self::assertGreaterThanOrEqual(97000, $file1Info);
        self::assertLessThanOrEqual(103000, $file1Info);

        $file2Info = filesize($this->outputDir . '/crop_002.jpg');
        self::assertGreaterThanOrEqual(97000, $file2Info);
        self::assertLessThanOrEqual(103000, $file2Info);
    }

    public function testExtractCropsFromEachPdfPageCorrectly(): void
    {
        $inputSource = new PathInput(TestingUtilities::getV2ProductDir() . '/crop/multipage_sample.pdf');
        $cropParams = new CropParameters($this->cropModelId);

        $response = $this->client->enqueueAndGetResult(CropResponse::class, $inputSource, $cropParams);
        $cropOperation = new Crop($inputSource);
        $extractedImages = $cropOperation->extractMultipleCrops($response->inference->result->crops);

        self::assertCount(5, $extractedImages);
        self::assertSame('multipage_sample.pdf_page0-0.jpg', $extractedImages[0]->filename);
        self::assertSame('multipage_sample.pdf_page1-0.jpg', $extractedImages[3]->filename);
    }
}
