<?php

declare(strict_types=1);

namespace V2\Search;

use DateTimeImmutable;
use Mindee\V2\Search\RagDocuments\RagDocumentSearchResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

class RagDocumentSearchTest extends TestCase
{
    public function testRagDocumentSearchResponse_LoadsLocally(): void
    {
        $fullPath = TestingUtilities::getV2DataDir() . "/search/rag_documents.json";
        $content = file_get_contents($fullPath);
        $json = json_decode($content, true);
        $response = new RagDocumentSearchResponse($json);

        self::assertNotNull($response);

        self::assertCount(3, $response->ragDocuments);
        self::assertEquals(3, $response->pagination->totalItems);
        self::assertEquals(1, $response->pagination->page);
        self::assertEquals(50, $response->pagination->perPage);
        self::assertEquals(1, $response->pagination->totalPages);

        $firstItem = $response->ragDocuments[0];
        self::assertEquals("cc831599-c545-48b7-aa27-6d7ccd5b8d32", $firstItem->id);
        self::assertEquals("12345678-1234-1234-1234-123456789abc", $firstItem->modelId);
        self::assertEquals("invoice_01.pdf", $firstItem->filename);
        self::assertEquals(new DateTimeImmutable("2026-06-30T13:13:46.168586Z"), $firstItem->createdAt);
        self::assertEquals(0, $firstItem->totalMatches);
        self::assertNull($firstItem->lastMatchAt);
        self::assertEquals("Processing", $firstItem->status);

        $secondItem = $response->ragDocuments[1];
        self::assertEquals("27467e4c-5602-4315-90d9-3d2da69b05ab", $secondItem->id);
        self::assertEquals("12345678-1234-1234-1234-123456789abc", $secondItem->modelId);
        self::assertEquals("invoice_02.pdf", $secondItem->filename);
        self::assertEquals(new DateTimeImmutable("2026-06-30T13:13:46.168586Z"), $secondItem->createdAt);
        self::assertEquals(0, $secondItem->totalMatches);
        self::assertNull($secondItem->lastMatchAt);
        self::assertEquals("Draft", $secondItem->status);

        $thirdItem = $response->ragDocuments[2];
        self::assertEquals("a6bcae7d-0439-476b-8a63-5a39ec05dc21", $thirdItem->id);
        self::assertEquals("12345678-1234-1234-1234-jobid1234567", $thirdItem->modelId);
        self::assertEquals("invoice_03.pdf", $thirdItem->filename);
        self::assertEquals(new DateTimeImmutable("2026-06-17T14:35:46.228006Z"), $thirdItem->createdAt);
        self::assertEquals(5, $thirdItem->totalMatches);
        self::assertEquals(new DateTimeImmutable("2026-06-18T14:35:46.248006Z"), $thirdItem->lastMatchAt);
        self::assertEquals("Active", $thirdItem->status);
    }
}
