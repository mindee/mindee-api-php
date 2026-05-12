<?php

declare(strict_types=1);

namespace V2\Product;

use PHPUnit\Framework\TestCase;
use TestingUtilities;
use Mindee\V2\Product\Crop\CropResponse;
use Mindee\Geometry\Point;

require_once(__DIR__ . "/../../TestingUtilities.php");

/**
 * Crop unit tests.
 */
class CropTest extends TestCase
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
     * Ensures all line endings are identical before comparison so the test
     * behaves the same on every platform (LF vs CRLF).
     * @param string $input Input string to normalize.
     */
    private static function normalizeLineEndings(string $input): string
    {
        return str_replace(["\r\n", "\r"], "\n", $input);
    }

    /**
     * Should correctly map properties when reading a single crop JSON.
     */
    public function testCropWhenSingleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("crop/default_sample.json");
        $response = new CropResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        self::assertSame("12345678-1234-1234-1234-123456789abc", $inference->id);
        self::assertSame("test-model-id", $inference->model->id);
        self::assertSame("12345678-1234-1234-1234-jobid1234567", $inference->job->id);

        self::assertSame("sample.jpeg", $inference->file->name);
        self::assertSame(1, $inference->file->pageCount);
        self::assertSame("image/jpeg", $inference->file->mimeType);

        $crops = $inference->result->crops;
        self::assertNotNull($crops);
        self::assertCount(2, $crops);

        $firstCrop = $crops[0];
        self::assertSame("receipt", $firstCrop->objectType);
        self::assertSame(0, $firstCrop->location->page);

        $polygon = $firstCrop->location->polygon;
        self::assertCount(4, $polygon->getCoordinates());

        self::assertEquals(new Point(0.214, 0.036), $polygon->getCoordinates()[0]);
        self::assertEquals(new Point(0.476, 0.036), $polygon->getCoordinates()[1]);
        self::assertEquals(new Point(0.476, 0.949), $polygon->getCoordinates()[2]);
        self::assertEquals(new Point(0.214, 0.949), $polygon->getCoordinates()[3]);
    }

    /**
     * Should correctly map properties when reading a multiple crop JSON.
     */
    public function testCropWhenMultipleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("crop/crop_multiple.json");
        $response = new CropResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        $job = $inference->job;
        self::assertSame("12345678-1234-1234-1234-jobid1234567", $job->id);

        self::assertSame("12345678-1234-1234-1234-123456789abc", $inference->id);
        self::assertSame("test-model-id", $inference->model->id);

        self::assertSame("default_sample.jpg", $inference->file->name);
        self::assertSame(1, $inference->file->pageCount);
        self::assertSame("image/jpeg", $inference->file->mimeType);

        $crops = $inference->result->crops;
        self::assertNotNull($crops);
        self::assertCount(2, $crops);

        $firstCrop = $crops[0];
        self::assertSame("invoice", $firstCrop->objectType);
        self::assertSame(0, $firstCrop->location->page);

        $firstPolygon = $firstCrop->location->polygon;
        self::assertCount(4, $firstPolygon->getCoordinates());
        self::assertEquals(new Point(0.214, 0.079), $firstPolygon->getCoordinates()[0]);
        self::assertEquals(new Point(0.476, 0.079), $firstPolygon->getCoordinates()[1]);
        self::assertEquals(new Point(0.476, 0.979), $firstPolygon->getCoordinates()[2]);
        self::assertEquals(new Point(0.214, 0.979), $firstPolygon->getCoordinates()[3]);

        $secondCrop = $crops[1];
        self::assertSame("receipt", $secondCrop->objectType);
        self::assertSame(0, $secondCrop->location->page);

        $secondPolygon = $secondCrop->location->polygon;
        self::assertCount(4, $secondPolygon->getCoordinates());
        self::assertEquals(new Point(0.547, 0.15), $secondPolygon->getCoordinates()[0]);
        self::assertEquals(new Point(0.862, 0.15), $secondPolygon->getCoordinates()[1]);
        self::assertEquals(new Point(0.862, 0.97), $secondPolygon->getCoordinates()[2]);
        self::assertEquals(new Point(0.547, 0.97), $secondPolygon->getCoordinates()[3]);
    }

    /**
     * crop_single.rst – RST display must be parsed and exposed
     */
    public function testRstDisplayMustBeAccessible(): void
    {
        $jsonSample = self::getInference("crop/crop_single.json");
        $response = new CropResponse($jsonSample);

        $rstReferencePath = TestingUtilities::getV2ProductDir() . "/crop/crop_single.rst";
        $rstReference = file_get_contents($rstReferencePath);

        $inference = $response->inference;
        self::assertNotNull($inference);

        self::assertSame(
            self::normalizeLineEndings($rstReference),
            self::normalizeLineEndings((string) $inference)
        );
    }
}
