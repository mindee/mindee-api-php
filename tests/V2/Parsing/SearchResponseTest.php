<?php


declare(strict_types=1);

namespace V2\Parsing;

use DateTime;
use Mindee\V2\Parsing\Error\ErrorItem;
use Mindee\V2\Parsing\Error\ErrorResponse;
use Mindee\V2\Parsing\Job\JobResponse;
use Mindee\V2\Parsing\Search\SearchModel;
use Mindee\V2\Parsing\Search\SearchResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

/**
 * InferenceV2 – field integrity checks
 */
class SearchResponseTest extends TestCase
{
    public function testSearchResponse(): void
    {
        $fullPath = TestingUtilities::getV2DataDir() . "/search/models.json";
        $content = file_get_contents($fullPath);
        $json = json_decode($content, true);
        $response = new SearchResponse($json);
        self::assertNotEmpty($response->models);
        foreach ($response->models as $model) {
            self::assertInstanceOf(SearchModel::class, $model);
            self::assertNotEmpty($model->id);
            self::assertNotEmpty($model->name);
        }
        self::assertCount(2, $response->models[0]->webhooks);
        self::assertEquals("https://failure.mindee.com", $response->models[0]->webhooks[0]->url);

        self::assertEquals(50, $response->paginationMetadata->perPage);
        self::assertEquals(1, $response->paginationMetadata->page);
        self::assertGreaterThanOrEqual(5, $response->paginationMetadata->totalItems);
        self::assertEquals(1, $response->paginationMetadata->totalPages);
    }
}
