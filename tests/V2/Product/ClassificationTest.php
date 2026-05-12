<?php

declare(strict_types=1);

namespace V2\Product;

use PHPUnit\Framework\TestCase;
use TestingUtilities;
use Mindee\V2\Product\Classification\ClassificationResponse;

require_once(__DIR__ . "/../../TestingUtilities.php");

/**
 * Classification unit tests.
 */
class ClassificationTest extends TestCase
{
    /**
     * Load a JSON sample and return its decoded contents.
     *
     * @return array Decoded JSON data.
     */
    private static function getInference(): array
    {
        $fullPath = TestingUtilities::getV2ProductDir() . "/classification/default_sample.json";
        $content = file_get_contents($fullPath);
        return json_decode($content, true);
    }

    /**
     * Helper to assert the core inference response properties exist.
     * @param mixed $response The response object to check.
     */
    private function assertInferenceResponse(mixed $response): void
    {
        self::assertNotNull($response->inference);
        self::assertNotNull($response->inference->id);
        self::assertNotNull($response->inference->file);
        self::assertNotNull($response->inference->result);
    }

    /**
     * Should correctly map properties when reading a single classification JSON.
     */
    public function testClassificationWhenSingleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference();

        $response = new ClassificationResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        self::assertSame("12345678-1234-1234-1234-123456789abc", $inference->id);
        self::assertSame("test-model-id", $inference->model->id);
        self::assertSame("12345678-1234-1234-1234-jobid1234567", $inference->job->id);

        self::assertSame("default_sample.jpg", $inference->file->name);
        self::assertSame(1, $inference->file->pageCount);
        self::assertSame("image/jpeg", $inference->file->mimeType);

        $classification = $inference->result->classification;
        self::assertSame("invoice", $classification->documentType);
    }
}
