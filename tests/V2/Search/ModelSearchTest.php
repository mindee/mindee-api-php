<?php

declare(strict_types=1);

namespace V2\Search;

use Mindee\V2\Parsing\Search\ModelSearchResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class ModelSearchTest extends TestCase
{
    public function testModelSearchResponse_LoadsLocally(): void
    {
        $fullPath = TestingUtilities::getV2DataDir() . "/search/models.json";
        $content = file_get_contents($fullPath);
        $json = json_decode($content, true);
        $response = new ModelSearchResponse($json);

        self::assertNotNull($response);

        self::assertCount(5, $response->models);
        self::assertEquals(5, $response->pagination->totalItems);
        self::assertEquals(1, $response->pagination->page);
        self::assertEquals(50, $response->pagination->perPage);
        self::assertEquals(1, $response->pagination->totalPages);

        $firstItem = $response->models[0];
        self::assertEquals("Extraction With Webhooks", $firstItem->name);
        self::assertEquals("afde5151-aa11-aa11-9289-fa04e50ca3b9", $firstItem->id);
        self::assertEquals("extraction", $firstItem->modelType);

        self::assertCount(2, $firstItem->webhooks);
        self::assertEquals("a2286ed9-aa11-aa11-bdc5-2f8496c5641a", $firstItem->webhooks[0]->id);
        self::assertEquals("FAILURE", $firstItem->webhooks[0]->name);
        self::assertEquals("https://failure.mindee.com", $firstItem->webhooks[0]->url);

        $lastItem = $response->models[4];
        self::assertEquals("Extraction Without Webhooks Key", $lastItem->name);
        self::assertEquals("e14e0923-ee55-ee55-a335-8d2110917d7b", $lastItem->id);
    }
}
