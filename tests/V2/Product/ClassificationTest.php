<?php

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
        $fullPath = TestingUtilities::getV2ProductDir() . "/classification/classification_single.json";
        $content = file_get_contents($fullPath);
        return json_decode($content, true);
    }

    /**
     * Helper to assert the core inference response properties exist.
     * @param mixed $response The response object to check.
     * @return void
     */
    private function assertInferenceResponse(mixed $response): void
    {
        $this->assertNotNull($response->inference);
        $this->assertNotNull($response->inference->id);
        $this->assertNotNull($response->inference->file);
        $this->assertNotNull($response->inference->result);
    }

    /**
     * Should correctly map properties when reading a single classification JSON.
     * @return void
     */
    public function testClassificationWhenSingleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference();

        $response = new ClassificationResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        $this->assertSame("12345678-1234-1234-1234-123456789abc", $inference->id);
        $this->assertSame("test-model-id", $inference->model->id);
        $this->assertSame("12345678-1234-1234-1234-jobid1234567", $inference->job->id);

        $this->assertSame("default_sample.jpg", $inference->file->name);
        $this->assertSame(1, $inference->file->pageCount);
        $this->assertSame("image/jpeg", $inference->file->mimeType);

        $classification = $inference->result->classification;
        $this->assertSame("invoice", $classification->documentType);
    }
}
