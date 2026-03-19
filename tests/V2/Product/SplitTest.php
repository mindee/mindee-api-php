<?php

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
     * Should correctly map properties when reading a single split JSON.
     * @return void
     */
    public function testSplitWhenSingleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("split/split_single.json");
        $response = new SplitResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        $model = $inference->model;
        $this->assertNotNull($model);

        $splits = $inference->result->splits;
        $this->assertNotNull($splits);
        $this->assertCount(1, $splits);

        $firstSplit = $splits[0];
        $this->assertSame("receipt", $firstSplit->documentType);

        $this->assertNotNull($firstSplit->pageRange);
        $this->assertCount(2, $firstSplit->pageRange);
        $this->assertSame(0, $firstSplit->pageRange[0]);
        $this->assertSame(0, $firstSplit->pageRange[1]);
    }

    /**
     * Should correctly map properties when reading a multiple split JSON.
     * @return void
     */
    public function testSplitWhenMultipleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("split/split_multiple.json");
        $response = new SplitResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        $model = $inference->model;
        $this->assertNotNull($model);

        $splits = $inference->result->splits;
        $this->assertNotNull($splits);
        $this->assertCount(3, $splits);

        $firstSplit = $splits[0];
        $this->assertSame("passport", $firstSplit->documentType);

        $this->assertNotNull($firstSplit->pageRange);
        $this->assertCount(2, $firstSplit->pageRange);
        $this->assertSame(0, $firstSplit->pageRange[0]);
        $this->assertSame(0, $firstSplit->pageRange[1]);

        $secondSplit = $splits[1];
        $this->assertSame("invoice", $secondSplit->documentType);

        $this->assertNotNull($secondSplit->pageRange);
        $this->assertCount(2, $secondSplit->pageRange);
        $this->assertSame(1, $secondSplit->pageRange[0]);
        $this->assertSame(3, $secondSplit->pageRange[1]);
    }
}
