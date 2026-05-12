<?php

declare(strict_types=1);

namespace V2\Product;

use Mindee\Input\PathInput;
use Mindee\V2\Client;
use Mindee\V2\Product\Classification\ClassificationResponse;
use Mindee\V2\Product\Classification\Params\ClassificationParameters;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class ClassificationFunctional extends TestCase
{
    private Client $client;
    private string $classificationModelId;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY');
        $this->client = new Client($apiKey);

        $this->classificationModelId = getenv('MINDEE_V2_CLASSIFICATION_MODEL_ID') ?: '';
    }

    /**
     * Tests the success of the classification process using a default sample file.
     *
     */
    public function testClassificationDefaultSampleMustSucceed(): void
    {
        $inputSource = new PathInput(
            TestingUtilities::getV2ProductDir() . '/classification/default_sample.jpg'
        );

        $productParams = new ClassificationParameters($this->classificationModelId);
        $response = $this->client->enqueueAndGetResult(ClassificationResponse::class, $inputSource, $productParams);

        self::assertNotNull($response);
        self::assertNotNull($response->inference);

        $file = $response->inference->file;
        self::assertNotNull($file);
        self::assertSame("default_sample.jpg", $file->name);

        $result = $response->inference->result;
        self::assertNotNull($result);

        $classifications = $result->classification;
        self::assertNotNull($classifications);
    }
}
