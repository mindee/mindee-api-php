<?php

declare(strict_types=1);

namespace V2\Parsing;

use DateTimeImmutable;
use Mindee\Geometry\Point;
use Mindee\Input\LocalResponse;
use Mindee\V2\Parsing\Error\ErrorItem;
use Mindee\V2\Parsing\Error\ErrorResponse;
use Mindee\V2\Parsing\FailedInferenceResponse;
use Mindee\V2\Parsing\Inference\Field\FieldConfidence;
use Mindee\V2\Parsing\Inference\Field\ListField;
use Mindee\V2\Parsing\Inference\Field\ObjectField;
use Mindee\V2\Parsing\Inference\Field\SimpleField;
use Mindee\V2\Parsing\Job\JobResponse;
use Mindee\V2\Product\Extraction\ExtractionResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

/**
 * Failed Inference Response test
 */
class FailedInferenceResponseTest extends TestCase
{
    /**
     * Should load.
     */
    public function testShouldLoad(): void
    {

        $fullPath = TestingUtilities::getV2DataDir() . "/errors/webhook_error_500_failed.json";
        $content = file_get_contents($fullPath);
        $jsonSample = json_decode($content, true);
        $response = new FailedInferenceResponse($jsonSample);
        self::assertInstanceOf(FailedInferenceResponse::class, $response);
        self::assertNotNull($response);
        self::assertSame("12345678-1234-1234-1234-123456789ABC", $response->inferenceId);
        self::assertSame("default_sample.jpg", $response->fileName);
        self::assertSame("dummy-alias.jpg", $response->fileAlias);
        self::assertInstanceOf(DateTimeImmutable::class, $response->createdAt);
        self::assertNotNull($response->error);
        self::assertInstanceOf(ErrorResponse::class, $response->error);
        self::assertSame(500, $response->error->status);
        self::assertSame("500-012", $response->error->code);
    }
}
