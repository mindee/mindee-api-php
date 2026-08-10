<?php

declare(strict_types=1);

namespace V2\Search;

use Mindee\V2\Client;
use Mindee\V2\Parsing\Search\ModelSearchResponse;
use Mindee\V2\Search\Models\ModelSearchParameters;
use PHPUnit\Framework\TestCase;

class ModelSearchFunctional extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(getenv('MINDEE_V2_API_KEY') ?: null);
    }

    public function testModelSearch_mustHaveResults(): void
    {
        $response = $this->client->search(ModelSearchResponse::class, new ModelSearchParameters());

        self::assertNotNull($response);
        self::assertNotNull($response->models);
        self::assertNotEmpty($response->models);
        self::assertNotNull($response->pagination);
        self::assertGreaterThan(1, $response->pagination->totalItems);
        self::assertEquals(1, $response->pagination->page);
    }

    public function testModelSearch_mustReturnEmpty(): void
    {
        $response = $this->client->search(
            ModelSearchResponse::class,
            new ModelSearchParameters(name: "je n'existe pas tralala")
        );

        self::assertNotNull($response);
        self::assertNotNull($response->models);
        self::assertEmpty($response->models);
        self::assertNotNull($response->pagination);
        self::assertEquals(0, $response->pagination->totalItems);
        self::assertEquals(1, $response->pagination->page);
    }

    public function testModelSearch_mustReturnEmptyObsolete(): void
    {
        /** @phpstan-ignore method.deprecated */
        $response = $this->client->searchModels("je n'existe pas tralala");

        self::assertNotNull($response);
        self::assertNotNull($response->models);
        self::assertEmpty($response->models);
        self::assertNotNull($response->pagination);
        self::assertEquals(0, $response->pagination->totalItems);
        self::assertEquals(1, $response->pagination->page);
    }
}
