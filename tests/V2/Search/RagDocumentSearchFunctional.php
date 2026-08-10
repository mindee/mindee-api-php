<?php

declare(strict_types=1);

namespace V2\Search;

use Mindee\V2\Client;
use Mindee\V2\Parsing\Search\RagDocumentSearchResponse;
use Mindee\V2\Search\RagDocuments\RagDocumentSearchParameters;
use PHPUnit\Framework\TestCase;

class RagDocumentSearchFunctional extends TestCase
{
    private Client $client;
    private string $findocModelId;

    protected function setUp(): void
    {
        $this->client = new Client(getenv('MINDEE_V2_API_KEY') ?: null);
        $this->findocModelId = getenv('MINDEE_V2_FINDOC_MODEL_ID') ?: '';
    }

    public function testRagDocumentSearch_mustHaveResults(): void
    {
        $response = $this->client->search(
            RagDocumentSearchResponse::class,
            new RagDocumentSearchParameters(modelId: $this->findocModelId)
        );

        self::assertNotNull($response);
        self::assertNotNull($response->ragDocuments);
        self::assertNotNull($response->pagination);
        self::assertEquals(1, $response->pagination->page);
    }
}
