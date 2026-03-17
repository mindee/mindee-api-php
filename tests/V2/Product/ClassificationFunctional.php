<?php


namespace V2\Product;

use Mindee\ClientV2;
use Mindee\Input\PathInput;
use Mindee\V2\Product\Classification\ClassificationResponse;
use Mindee\V2\Product\Classification\Params\ClassificationParameters;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class ClassificationFunctional extends TestCase
{
    private ClientV2 $client;
    private string $classificationModelId;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY');
        $this->client = new ClientV2($apiKey);

        $this->classificationModelId = getenv('MINDEE_V2_CLASSIFICATION_MODEL_ID') ?: '';
    }

    /**
     * Tests the success of the classification process using a default sample file.
     *
     * @return void
     */
    public function testClassificationDefaultSampleMustSucceed(): void
    {
        $inputSource = new PathInput(
            TestingUtilities::getV2ProductDir() . '/classification/default_invoice.jpg'
        );

        $productParams = new ClassificationParameters($this->classificationModelId);
        $response = $this->client->enqueueAndGetResult(ClassificationResponse::class, $inputSource, $productParams);

        $this->assertNotNull($response);
        $this->assertNotNull($response->inference);

        $file = $response->inference->file;
        $this->assertNotNull($file);
        $this->assertSame("default_invoice.jpg", $file->name);

        $result = $response->inference->result;
        $this->assertNotNull($result);

        $classifications = $result->classification;
        $this->assertNotNull($classifications);
    }
}
