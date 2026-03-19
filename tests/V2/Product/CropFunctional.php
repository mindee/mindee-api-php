<?php

namespace V2\Product;

use Mindee\ClientV2;
use Mindee\Input\PathInput;
use Mindee\V2\Product\Crop\CropResponse;
use Mindee\V2\Product\Crop\Params\CropParameters;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class CropFunctional extends TestCase
{
    private ClientV2 $client;
    private string $cropModelId;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY');
        $this->client = new ClientV2($apiKey);

        $this->cropModelId = getenv('MINDEE_V2_CROP_MODEL_ID') ?: '';
    }

    /**
     * Tests the success of the crop process using a default sample file.
     *
     * @return void
     */
    public function testCropDefaultSampleMustSucceed(): void
    {
        $inputSource = new PathInput(
            TestingUtilities::getV2ProductDir() . '/crop/default_sample.jpg'
        );

        $productParams = new CropParameters($this->cropModelId);
        $response = $this->client->enqueueAndGetResult(CropResponse::class, $inputSource, $productParams);

        $this->assertNotNull($response);
        $this->assertNotNull($response->inference);

        $file = $response->inference->file;
        $this->assertNotNull($file);
        $this->assertSame("default_sample.jpg", $file->name);

        $result = $response->inference->result;
        $this->assertNotNull($result);

        $crops = $result->crops;
        $this->assertNotNull($crops);
        $this->assertCount(2, $crops);

        foreach ($crops as $crop) {
            $this->assertNotNull($crop->objectType);
            $this->assertNotNull($crop->location);
        }
    }
}
