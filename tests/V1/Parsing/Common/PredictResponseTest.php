<?php

declare(strict_types=1);

namespace V1\Parsing\Common;

use Mindee\V1\Parsing\Common\PredictResponse;
use Mindee\V1\Product\Invoice\InvoiceV4;
use Mindee\V1\Product\Invoice\InvoiceV4Document;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class PredictResponseTest extends TestCase
{
    public function testLoadingFromJSONShouldCreateAPredictResponse(): void
    {
        $json = file_get_contents(
            TestingUtilities::getV1DataDir() . "/products/invoices/response_v4/complete.json"
        );
        $response = json_decode($json, true);
        $parsedResponse = new PredictResponse(InvoiceV4::class, $response);
        self::assertInstanceOf(InvoiceV4::class, $parsedResponse->document->inference);
        foreach ($parsedResponse->document->inference->pages as $page) {
            self::assertInstanceOf(InvoiceV4Document::class, $page->prediction);
        }
        self::assertSame(1, $parsedResponse->document->nPages);
    }
}
