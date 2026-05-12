<?php

declare(strict_types=1);

namespace V2\Product;

use Mindee\Input\PathInput;
use Mindee\V2\Client;
use Mindee\V2\Product\Crop\CropResponse;
use Mindee\V2\Product\Crop\Params\CropParameters;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class CropFunctional extends TestCase
{
    private Client $client;
    private string $cropModelId;
    private string $cropExtractionModelId;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY');
        $this->client = new Client($apiKey);

        $this->cropModelId = getenv('MINDEE_V2_CROP_MODEL_ID') ?: '';
        $this->cropExtractionModelId = getenv('MINDEE_V2_SE_TESTS_CROP_EXTRACTION_MODEL_ID') ?: '';
    }

    /**
     * Tests the success of the crop process using a default sample file.
     *
     */
    public function testCropDefaultSampleMustSucceed(): void
    {
        $inputSource = new PathInput(
            TestingUtilities::getV2ProductDir() . '/crop/default_sample.jpg'
        );

        $productParams = new CropParameters($this->cropModelId);
        $response = $this->client->enqueueAndGetResult(CropResponse::class, $inputSource, $productParams);

        self::assertNotNull($response);
        self::assertNotNull($response->inference);

        $file = $response->inference->file;
        self::assertNotNull($file);
        self::assertSame("default_sample.jpg", $file->name);

        $result = $response->inference->result;
        self::assertNotNull($result);

        $crops = $result->crops;
        self::assertNotNull($crops);
        self::assertCount(2, $crops);

        foreach ($crops as $crop) {
            self::assertNotNull($crop->objectType);
            self::assertNotNull($crop->location);
        }
    }

    /**
     * Tests the success of the crop and extraction process.
     *
     */
    public function testCropAndExtractionMustSucceed(): void
    {
        $inputSource = new PathInput(
            TestingUtilities::getV2ProductDir() . '/crop/default_sample.jpg'
        );

        $productParams = new CropParameters(
            $this->cropExtractionModelId,
            "nodejs_integration-test_crop_multipage"
        );

        $response = $this->client->enqueueAndGetResult(
            CropResponse::class,
            $inputSource,
            $productParams
        );

        self::assertNotNull($response);
        $inference = $response->inference;
        self::assertNotNull($inference);

        $file = $inference->file;
        self::assertNotNull($file);
        self::assertSame("default_sample.jpg", $file->name);
        self::assertSame(1, $file->pageCount);

        self::assertNotNull($inference->model);
        self::assertSame($this->cropExtractionModelId, $inference->model->id);

        $result = $inference->result;
        self::assertNotNull($result);
        self::assertCount(2, $result->crops);

        $crop0 = $result->crops[0];
        self::assertSame("receipt", $crop0->objectType);
        self::assertNotNull($crop0->location->polygon);
        self::assertSame(0, $crop0->location->page);
        $extractionResponse0 = $crop0->extractionResponse;
        self::assertNotNull($extractionResponse0);

        $supplierName = $extractionResponse0->inference->result->fields
            ->getSimpleField("supplier_name")->value;

        self::assertSame("CHEZ ALAIN MIAM MIAM", $supplierName);
    }
}
