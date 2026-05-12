<?php

declare(strict_types=1);

namespace V2\Product;

use Mindee\Input\PathInput;
use Mindee\V2\Client;
use Mindee\V2\Product\Split\Params\SplitParameters;
use Mindee\V2\Product\Split\SplitResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class SplitFunctional extends TestCase
{
    private Client $client;
    private string $splitModelId;

    protected function setUp(): void
    {
        $apiKey = getenv('MINDEE_V2_API_KEY');
        $this->client = new Client($apiKey);

        $this->splitModelId = getenv('MINDEE_V2_SPLIT_MODEL_ID') ?: '';
    }

    /**
     * Tests the success of the split process using a default sample file.
     *
     */
    public function testSplitDefaultSampleMustSucceed(): void
    {
        $inputSource = new PathInput(
            TestingUtilities::getV2ProductDir() . '/split/default_sample.pdf'
        );

        $productParams = new SplitParameters($this->splitModelId);
        $response = $this->client->enqueueAndGetResult(SplitResponse::class, $inputSource, $productParams);

        self::assertNotNull($response);
        self::assertNotNull($response->inference);

        $file = $response->inference->file;
        self::assertNotNull($file);
        self::assertSame("default_sample.pdf", $file->name);

        $result = $response->inference->result;
        self::assertNotNull($result);

        $splits = $result->splits;
        self::assertNotNull($splits);
        self::assertCount(2, $splits);
    }
}
