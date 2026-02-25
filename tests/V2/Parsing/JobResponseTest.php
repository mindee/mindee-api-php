<?php

namespace V2\Parsing;

use DateTime;
use Mindee\Error\ErrorItem;
use Mindee\Parsing\V2\ErrorResponse;
use Mindee\Parsing\V2\JobResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class TestJobResponse extends TestCase
{
    /**
     * Load a job sample JSON file and return its decoded contents.
     *
     * @param string $jsonFile Name of the JSON file to load.
     * @return array Decoded JSON data.
     */
    private static function getJobSamples(string $jsonFile): array
    {
        $fullPath = TestingUtilities::getV2DataDir() . "/job/$jsonFile";
        $content = file_get_contents($fullPath);
        return json_decode($content, true);
    }

    /**
     * Should load when status is Processing.
     * @return void
     */
    public function testShouldLoadWhenStatusIsProcessing(): void
    {
        $jsonSample = self::getJobSamples('ok_processing.json');
        $response = new JobResponse($jsonSample);

        $this->assertNotNull($response->job);
        $this->assertSame('Processing', $response->job->status);
        $this->assertNull($response->job->completedAt);
        $this->assertNull($response->job->error);
    }

    /**
     * Should load when status is Processed.
     * @return void
     */
    public function testShouldLoadWhenStatusIsProcessed(): void
    {
        $jsonSample = self::getJobSamples('ok_processed_webhooks_ok.json');
        $response = new JobResponse($jsonSample);

        $this->assertNotNull($response->job);
        $this->assertSame('Processed', $response->job->status);
        $this->assertInstanceOf(DateTime::class, $response->job->completedAt);
        $this->assertNull($response->job->error);
    }

    /**
     * Should load with 422 error.
     * @return void
     */
    public function testShouldLoadWith422Error(): void
    {
        $jsonSample = self::getJobSamples('fail_422.json');
        $response = new JobResponse($jsonSample);

        $this->assertNotNull($response->job);
        $this->assertSame('Failed', $response->job->status);
        $this->assertInstanceOf(DateTime::class, $response->job->completedAt);

        $this->assertInstanceOf(ErrorResponse::class, $response->job->error);
        $this->assertSame(422, $response->job->error->status);
        $this->assertStringStartsWith('422-', $response->job->error->code);
        $this->assertIsArray($response->job->error->errors);
        $this->assertCount(1, $response->job->error->errors);
        $this->assertInstanceOf(ErrorItem::class, $response->job->error->errors[0]);
    }
}
