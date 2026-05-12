<?php

declare(strict_types=1);

namespace V2\Product;

use PHPUnit\Framework\TestCase;
use TestingUtilities;
use Mindee\V2\Product\Split\SplitResponse;

require_once(__DIR__ . "/../../TestingUtilities.php");

/**
 * Split unit tests.
 */
class SplitTest extends TestCase
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
     * Should correctly map properties when reading a single split JSON.
     */
    public function testSplitWhenSingleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("split/split_single.json");
        $response = new SplitResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        $model = $inference->model;
        self::assertNotNull($model);

        $splits = $inference->result->splits;
        self::assertNotNull($splits);
        self::assertCount(1, $splits);

        $firstSplit = $splits[0];
        self::assertSame("receipt", $firstSplit->documentType);

        self::assertNotNull($firstSplit->pageRange);
        self::assertCount(2, $firstSplit->pageRange);
        self::assertSame(0, $firstSplit->pageRange[0]);
        self::assertSame(0, $firstSplit->pageRange[1]);
    }

    /**
     * Should correctly map properties when reading a multiple split JSON.
     */
    public function testSplitWhenMultipleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("split/split_multiple.json");
        $response = new SplitResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        $model = $inference->model;
        self::assertNotNull($model);

        $splits = $inference->result->splits;
        self::assertNotNull($splits);
        self::assertCount(3, $splits);

        $firstSplit = $splits[0];
        self::assertSame("passport", $firstSplit->documentType);

        self::assertNotNull($firstSplit->pageRange);
        self::assertCount(2, $firstSplit->pageRange);
        self::assertSame(0, $firstSplit->pageRange[0]);
        self::assertSame(0, $firstSplit->pageRange[1]);

        $secondSplit = $splits[1];
        self::assertSame("invoice", $secondSplit->documentType);

        self::assertNotNull($secondSplit->pageRange);
        self::assertCount(2, $secondSplit->pageRange);
        self::assertSame(1, $secondSplit->pageRange[0]);
        self::assertSame(3, $secondSplit->pageRange[1]);
    }
}
