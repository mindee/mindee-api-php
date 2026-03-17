<?php


namespace V2\Product;

use PHPUnit\Framework\TestCase;
use TestingUtilities;
use Mindee\V2\Product\Crop\CropResponse;
use Mindee\Geometry\Point;

// Added for the polygon coordinate assertions

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
     * Ensures all line endings are identical before comparison so the test
     * behaves the same on every platform (LF vs CRLF).
     * @param string $input Input string to normalize.
     * @return string
     */
    private static function normalizeLineEndings(string $input): string
    {
        return str_replace(["\r\n", "\r"], "\n", $input);
    }

    /**
     * Should correctly map properties when reading a single crop JSON.
     * @return void
     */
    public function testCropWhenSingleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("crop/crop_single.json");
        $response = new CropResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        $this->assertSame("12345678-1234-1234-1234-123456789abc", $inference->id);
        $this->assertSame("test-model-id", $inference->model->id);
        $this->assertSame("12345678-1234-1234-1234-jobid1234567", $inference->job->id);

        $this->assertSame("sample.jpeg", $inference->file->name);
        $this->assertSame(1, $inference->file->pageCount);
        $this->assertSame("image/jpeg", $inference->file->mimeType);

        $crops = $inference->result->crops;
        $this->assertNotNull($crops);
        $this->assertCount(1, $crops);

        $firstCrop = $crops[0];
        $this->assertSame("invoice", $firstCrop->objectType);
        $this->assertSame(0, $firstCrop->location->page);

        $polygon = $firstCrop->location->polygon;
        $this->assertCount(4, $polygon->getCoordinates());

        // Note: Using assertEquals here instead of assertSame to allow for object value comparison
        $this->assertEquals(new Point(0.15, 0.254), $polygon->getCoordinates()[0]);
        $this->assertEquals(new Point(0.85, 0.254), $polygon->getCoordinates()[1]);
        $this->assertEquals(new Point(0.85, 0.947), $polygon->getCoordinates()[2]);
        $this->assertEquals(new Point(0.15, 0.947), $polygon->getCoordinates()[3]);
    }

    /**
     * Should correctly map properties when reading a multiple crop JSON.
     * @return void
     */
    public function testCropWhenMultipleMustHaveValidProperties(): void
    {
        $jsonSample = self::getInference("crop/crop_multiple.json");
        $response = new CropResponse($jsonSample);

        $this->assertInferenceResponse($response);

        $inference = $response->inference;

        $job = $inference->job;
        $this->assertSame("12345678-1234-1234-1234-jobid1234567", $job->id);

        $this->assertSame("12345678-1234-1234-1234-123456789abc", $inference->id);
        $this->assertSame("test-model-id", $inference->model->id);

        $this->assertSame("default_sample.jpg", $inference->file->name);
        $this->assertSame(1, $inference->file->pageCount);
        $this->assertSame("image/jpeg", $inference->file->mimeType);

        $crops = $inference->result->crops;
        $this->assertNotNull($crops);
        $this->assertCount(2, $crops);

        $firstCrop = $crops[0];
        $this->assertSame("invoice", $firstCrop->objectType);
        $this->assertSame(0, $firstCrop->location->page);

        $firstPolygon = $firstCrop->location->polygon;
        $this->assertCount(4, $firstPolygon->getCoordinates());
        $this->assertEquals(new Point(0.214, 0.079), $firstPolygon->getCoordinates()[0]);
        $this->assertEquals(new Point(0.476, 0.079), $firstPolygon->getCoordinates()[1]);
        $this->assertEquals(new Point(0.476, 0.979), $firstPolygon->getCoordinates()[2]);
        $this->assertEquals(new Point(0.214, 0.979), $firstPolygon->getCoordinates()[3]);

        $secondCrop = $crops[1];
        $this->assertSame("receipt", $secondCrop->objectType);
        $this->assertSame(0, $secondCrop->location->page);

        $secondPolygon = $secondCrop->location->polygon;
        $this->assertCount(4, $secondPolygon->getCoordinates());
        $this->assertEquals(new Point(0.547, 0.15), $secondPolygon->getCoordinates()[0]);
        $this->assertEquals(new Point(0.862, 0.15), $secondPolygon->getCoordinates()[1]);
        $this->assertEquals(new Point(0.862, 0.97), $secondPolygon->getCoordinates()[2]);
        $this->assertEquals(new Point(0.547, 0.97), $secondPolygon->getCoordinates()[3]);
    }

    /**
     * crop_single.rst – RST display must be parsed and exposed
     * @return void
     */
    public function testRstDisplayMustBeAccessible(): void
    {
        $jsonSample = self::getInference("crop/crop_single.json");
        $response = new CropResponse($jsonSample);

        $rstReferencePath = TestingUtilities::getV2ProductDir() . "/crop/crop_single.rst";
        $rstReference = file_get_contents($rstReferencePath);

        $inference = $response->inference;
        $this->assertNotNull($inference);

        // Assumes your Inference class implements the __toString() magic method
        // which maps to C#'s ToString()
        $this->assertEquals(
            self::normalizeLineEndings($rstReference),
            self::normalizeLineEndings((string)$inference)
        );
    }
}
