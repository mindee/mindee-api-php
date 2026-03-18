<?php

namespace V2\Product;

use Mindee\ClientV2;
use Mindee\Input\PathInput;
use Mindee\V2\Product\Ocr\OcrResponse;
use Mindee\V2\Product\Ocr\Params\OcrParameters;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class OcrFunctional extends TestCase
{
    private ClientV2 $client;
    private string $ocrModelId;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY');
        $this->client = new ClientV2($apiKey);

        $this->ocrModelId = getenv('MINDEE_V2_OCR_MODEL_ID') ?: '';
    }

    /**
     * Tests the success of the OCR process using a default sample file.
     *
     * @return void
     */
    public function testOcrDefaultSampleMustSucceed(): void
    {
        $inputSource = new PathInput(
            TestingUtilities::getV2ProductDir() . '/ocr/default_sample.jpg'
        );

        $productParams = new OcrParameters($this->ocrModelId);
        $response = $this->client->enqueueAndGetResult(OcrResponse::class, $inputSource, $productParams);

        $this->assertNotNull($response);
        $this->assertNotNull($response->inference);

        $file = $response->inference->file;
        $this->assertNotNull($file);
        $this->assertSame("default_sample.jpg", $file->name);

        $result = $response->inference->result;
        $this->assertNotNull($result);

        $pages = $result->pages;
        $this->assertNotNull($pages);
        $this->assertCount(1, $pages);
    }
}