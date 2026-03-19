<?php

namespace V2\Product;

use PHPUnit\Framework\TestCase;
use TestingUtilities;
use Mindee\V2\Product\Ocr\OcrResponse;

require_once(__DIR__ . "/../../TestingUtilities.php");

/**
 * OCR unit tests.
 */
class OcrTest extends TestCase
{
    /**
     * Load a JSON sample and return its decoded contents.
     *
     * @param string $path Path to the JSON file to load relative to the product dir.
     * @return array Decoded JSON data.
     */
    private static function getInference(string $path): array
    {
        $fullPath = TestingUtilities::getV2ProductDir() . "/" . $path;
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
     * Should correctly map properties when reading a single OCR JSON.
     * @return void
     */
    public function testOcrWhenSingleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("ocr/ocr_single.json");
        $response = new OcrResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        $this->assertSame("12345678-1234-1234-1234-123456789abc", $inference->id);
        $this->assertSame("test-model-id", $inference->model->id);

        $this->assertSame("default_sample.jpg", $inference->file->name);
        $this->assertSame(1, $inference->file->pageCount);
        $this->assertSame("image/jpeg", $inference->file->mimeType);

        $pages = $inference->result->pages;
        $this->assertNotNull($pages);
        $this->assertCount(1, $pages);

        $firstPage = $pages[0];
        $this->assertNotNull($firstPage->words);

        $firstWord = $firstPage->words[0];
        $this->assertSame("Shipper:", $firstWord->content);
        // Using the getCoordinates() logic from the corrected file
        $this->assertCount(4, $firstWord->polygon->getCoordinates());

        $fifthWord = $firstPage->words[4];
        $this->assertSame("INC.", $fifthWord->content);
        $this->assertCount(4, $fifthWord->polygon->getCoordinates());
    }

    /**
     * Should correctly map properties when reading a multiple OCR JSON.
     * @return void
     */
    public function testOcrWhenMultipleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("ocr/ocr_multiple.json");
        $response = new OcrResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        $job = $inference->job;
        $this->assertSame("12345678-1234-1234-1234-jobid1234567", $job->id);

        $model = $inference->model;
        $this->assertNotNull($model);

        $pages = $inference->result->pages;
        $this->assertNotNull($pages);
        $this->assertCount(3, $pages);

        foreach ($pages as $page) {
            $this->assertNotNull($page->words);
            $this->assertNotNull($page->content);
            $this->assertIsString($page->content);
        }
    }
}
