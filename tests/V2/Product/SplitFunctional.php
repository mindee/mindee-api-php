<?php

namespace V2\Product;

use Mindee\ClientV2;
use Mindee\Input\PathInput;
use Mindee\V2\Product\Split\Params\SplitParameters;
use Mindee\V2\Product\Split\SplitResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class SplitFunctional extends TestCase
{
    private ClientV2 $client;
    private string $splitModelId;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY');
        $this->client = new ClientV2($apiKey);

        $this->splitModelId = getenv('MINDEE_V2_SPLIT_MODEL_ID') ?: '';
    }

    /**
     * Tests the success of the split process using a default sample file.
     *
     * @return void
     */
    public function testSplitDefaultSampleMustSucceed(): void
    {
        // Matched exactly to the C# Constants.V2RootDir pathing
        $inputSource = new PathInput(
            TestingUtilities::getV2RootDir() . '/products/split/default_sample.pdf'
        );

        $productParams = new SplitParameters($this->splitModelId);
        $response = $this->client->enqueueAndGetResult(SplitResponse::class, $inputSource, $productParams);

        $this->assertNotNull($response);
        $this->assertNotNull($response->inference);

        $file = $response->inference->file;
        $this->assertNotNull($file);
        $this->assertSame("default_sample.pdf", $file->name);

        $result = $response->inference->result;
        $this->assertNotNull($result);

        $splits = $result->splits;
        $this->assertNotNull($splits);
        $this->assertCount(2, $splits);
    }
}
