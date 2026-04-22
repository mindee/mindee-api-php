<?php

namespace V2\FileOperations;

use Mindee\ClientV2;
use Mindee\Input\InferenceParameters;
use Mindee\Input\PathInput;
use Mindee\Parsing\V2\InferenceResponse;
use Mindee\V2\FileOperations\Crop;
use Mindee\V2\Product\Crop\CropResponse;
use Mindee\V2\Product\Crop\Params\CropParameters;
use PHPUnit\Framework\TestCase;

class CropFunctional extends TestCase
{
    private ClientV2 $client;
    private string $cropModelId;
    private string $findocModelId;
    private string $outputDir;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY') ?: '';
        $this->client = new ClientV2($apiKey);
        $this->cropModelId = getenv('MINDEE_V2_CROP_MODEL_ID') ?: '';
        $this->findocModelId = getenv('MINDEE_V2_FINDOC_MODEL_ID') ?: '';

        $this->outputDir = getcwd() . '/output';
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0777, true);
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

    private function checkFindocReturn(InferenceResponse $findocResponse): void
    {
        $this->assertGreaterThan(0, strlen($findocResponse->inference->model->id));

        $totalAmount = $findocResponse->inference->result->fields['total_amount'];
        $this->assertNotNull($totalAmount);
        $this->assertGreaterThan(0, $totalAmount->value);
    }

    public function testExtractCropsFromImageCorrectly(): void
    {
        $inputSource = new PathInput(\TestingUtilities::getV2ProductDir() . '/crop/default_sample.jpg');
        $cropParams = new CropParameters($this->cropModelId);

        $response = $this->client->enqueueAndGetResult(CropResponse::class, $inputSource, $cropParams);

        $this->assertNotNull($response);
        $this->assertCount(2, $response->inference->result->crops);

        $cropOperation = new Crop($inputSource);
        $extractedImages = $cropOperation->extractCrops($response->inference->result->crops);

        $this->assertCount(2, $extractedImages);
        $this->assertEquals('default_sample.jpg_page0-0.jpg', $extractedImages[0]->filename);
        $this->assertEquals('default_sample.jpg_page0-1.jpg', $extractedImages[1]->filename);

        $extractionInput = $extractedImages[0]->asInputSource();
        $findocParams = new InferenceParameters($this->findocModelId);

        $invoice0 = $this->client->enqueueAndGetResult(InferenceResponse::class, $extractionInput, $findocParams);

        $this->checkFindocReturn($invoice0);

        $extractedImages->saveAllToDisk($this->outputDir, quality: 50);

        $file1Info = filesize($this->outputDir . '/crop_001.jpg');
        $this->assertGreaterThanOrEqual(98000, $file1Info);
        $this->assertLessThanOrEqual(110000, $file1Info);

        $file2Info = filesize($this->outputDir . '/crop_002.jpg');
        $this->assertGreaterThanOrEqual(98000, $file2Info);
        $this->assertLessThanOrEqual(110000, $file2Info);
    }

    public function testExtractCropsFromEachPdfPageCorrectly(): void
    {
        $inputSource = new PathInput(\TestingUtilities::getV2ProductDir() . '/crop/multipage_sample.pdf');
        $cropParams = new CropParameters($this->cropModelId);

        $response = $this->client->enqueueAndGetResult(CropResponse::class, $inputSource, $cropParams);
        $cropOperation = new Crop($inputSource);
        $extractedImages = $cropOperation->extractCrops($response->inference->result->crops);

        $this->assertCount(5, $extractedImages);
        $this->assertEquals('multipage_sample.pdf_page0-0.jpg', $extractedImages[0]->filename);
        $this->assertEquals('multipage_sample.pdf_page1-0.jpg', $extractedImages[3]->filename);
    }
}
