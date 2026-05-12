<?php

declare(strict_types=1);

namespace V2\Parsing;

use DateTime;
use Mindee\V2\Parsing\ErrorItem;
use Mindee\V2\Parsing\ErrorResponse;
use Mindee\V2\Parsing\JobResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class JobResponseTest extends TestCase
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
     */
    public function testShouldLoadWhenStatusIsProcessing(): void
    {
        $jsonSample = self::getJobSamples('ok_processing.json');
        $response = new JobResponse($jsonSample);

        self::assertNotNull($response->job);
        self::assertSame('Processing', $response->job->status);
        self::assertNull($response->job->completedAt);
        self::assertNull($response->job->error);
        self::assertIsArray($response->job->webhooks);
        self::assertCount(0, $response->job->webhooks);
    }

    /**
     * Should load when status is Processed.
     */
    public function testShouldLoadWhenStatusIsProcessed(): void
    {
        $jsonSample = self::getJobSamples('ok_processed_webhooks_ok.json');
        $response = new JobResponse($jsonSample);

        self::assertNotNull($response->job);
        self::assertSame('Processed', $response->job->status);
        self::assertInstanceOf(DateTime::class, $response->job->completedAt);
        self::assertNull($response->job->error);
    }

    /**
     * Should load with 422 error.
     */
    public function testShouldLoadWith422Error(): void
    {
        $jsonSample = self::getJobSamples('fail_422.json');
        $response = new JobResponse($jsonSample);

        self::assertNotNull($response->job);
        self::assertSame('Failed', $response->job->status);
        self::assertInstanceOf(DateTime::class, $response->job->completedAt);

        self::assertInstanceOf(ErrorResponse::class, $response->job->error);
        self::assertSame(422, $response->job->error->status);
        self::assertStringStartsWith('422-', $response->job->error->code);
        self::assertIsArray($response->job->error->errors);
        self::assertCount(1, $response->job->error->errors);
        self::assertInstanceOf(ErrorItem::class, $response->job->error->errors[0]);
    }
}
