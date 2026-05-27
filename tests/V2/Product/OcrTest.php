<?php

declare(strict_types=1);

namespace V2\Product;

use PHPUnit\Framework\TestCase;
use TestingUtilities;
use Mindee\V2\Product\Ocr\OcrResponse;

require_once(__DIR__ . "/../../TestingUtilities.php");

/**
 * Ocr unit tests.
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
     */
    private function assertInferenceResponse(mixed $response): void
    {
        self::assertNotNull($response->inference);
        self::assertNotNull($response->inference->id);
        self::assertNotNull($response->inference->file);
        self::assertNotNull($response->inference->result);
    }

    /**
     * Should correctly map properties when reading a single Ocr JSON.
     */
    public function testOcrWhenSingleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("ocr/ocr_single.json");
        $response = new OcrResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        self::assertSame("12345678-1234-1234-1234-123456789abc", $inference->id);
        self::assertSame("test-model-id", $inference->model->id);

        self::assertSame("default_sample.jpg", $inference->file->name);
        self::assertSame(1, $inference->file->pageCount);
        self::assertSame("image/jpeg", $inference->file->mimeType);

        $pages = $inference->result->pages;
        self::assertNotNull($pages);
        self::assertCount(1, $pages);

        $firstPage = $pages[0];
        self::assertNotNull($firstPage->words);

        $firstWord = $firstPage->words[0];
        self::assertSame("Shipper:", $firstWord->content);
        self::assertCount(4, $firstWord->polygon->getCoordinates());

        $fifthWord = $firstPage->words[4];
        self::assertSame("INC.", $fifthWord->content);
        self::assertCount(4, $fifthWord->polygon->getCoordinates());
    }

    /**
     * Should correctly map properties when reading a multiple Ocr JSON.
     */
    public function testOcrWhenMultipleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("ocr/ocr_multiple.json");
        $response = new OcrResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        $job = $inference->job;
        self::assertSame("12345678-1234-1234-1234-jobid1234567", $job->id);

        $model = $inference->model;
        self::assertNotNull($model);

        $pages = $inference->result->pages;
        self::assertNotNull($pages);
        self::assertCount(3, $pages);

        foreach ($pages as $page) {
            self::assertNotNull($page->words);
            self::assertNotNull($page->content);
            self::assertIsString($page->content);
        }
    }
}
