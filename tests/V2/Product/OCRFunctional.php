<?php

declare(strict_types=1);

namespace V2\Product;

use Mindee\Input\PathInput;
use Mindee\V2\Client;
use Mindee\V2\Product\OCR\OCRResponse;
use Mindee\V2\Product\OCR\Params\OCRParameters;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class OCRFunctional extends TestCase
{
    private Client $client;
    private string $ocrModelId;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY');
        $this->client = new Client($apiKey);

        $this->ocrModelId = getenv('MINDEE_V2_OCR_MODEL_ID') ?: '';
    }

    /**
     * Tests the success of the OCR process using a default sample file.
     *
     */
    public function testOCRDefaultSampleMustSucceed(): void
    {
        $inputSource = new PathInput(
            TestingUtilities::getV2ProductDir() . '/ocr/default_sample.jpg'
        );

        $productParams = new OCRParameters($this->ocrModelId);
        $response = $this->client->enqueueAndGetResult(OCRResponse::class, $inputSource, $productParams);

        self::assertNotNull($response);
        self::assertNotNull($response->inference);

        $file = $response->inference->file;
        self::assertNotNull($file);
        self::assertSame("default_sample.jpg", $file->name);

        $result = $response->inference->result;
        self::assertNotNull($result);

        $pages = $result->pages;
        self::assertNotNull($pages);
        self::assertCount(1, $pages);
    }
}
