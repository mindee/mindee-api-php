<?php


declare(strict_types=1);

namespace V2\Parsing;

use Mindee\V2\Parsing\Search\RagDocument;
use Mindee\V2\Parsing\Search\RagDocumentSearchResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

/**
 * RagDocumentSearchResponse – field integrity checks
 */
class RagDocumentSearchResponseTest extends TestCase
{
    public function testRagDocumentSearchResponse(): void
    {
        $fullPath = TestingUtilities::getV2DataDir() . "/search/rag_documents.json";
        $content = file_get_contents($fullPath);
        $json = json_decode($content, true);
        $response = new RagDocumentSearchResponse($json);

        self::assertCount(3, $response->ragDocuments);
        foreach ($response->ragDocuments as $document) {
            self::assertInstanceOf(RagDocument::class, $document);
            self::assertNotEmpty($document->id);
            self::assertNotEmpty($document->modelId);
            self::assertNotEmpty($document->filename);
        }

        $firstItem = $response->ragDocuments[0];
        self::assertEquals("cc831599-c545-48b7-aa27-6d7ccd5b8d32", $firstItem->id);
        self::assertEquals("12345678-1234-1234-1234-123456789abc", $firstItem->modelId);
        self::assertEquals("invoice_01.pdf", $firstItem->filename);
        self::assertEquals(0, $firstItem->totalMatches);
        self::assertNull($firstItem->lastMatchAt);
        self::assertEquals("Processing", $firstItem->status);

        $thirdItem = $response->ragDocuments[2];
        self::assertEquals("a6bcae7d-0439-476b-8a63-5a39ec05dc21", $thirdItem->id);
        self::assertEquals("invoice_03.pdf", $thirdItem->filename);
        self::assertEquals(5, $thirdItem->totalMatches);
        self::assertNotNull($thirdItem->lastMatchAt);
        self::assertEquals("Active", $thirdItem->status);

        self::assertEquals(50, $response->pagination->perPage);
        self::assertEquals(1, $response->pagination->page);
        self::assertEquals(3, $response->pagination->totalItems);
        self::assertEquals(1, $response->pagination->totalPages);
    }
}
