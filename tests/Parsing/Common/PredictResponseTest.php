<?php

namespace Parsing\Common;

use Mindee\Parsing\Common\PredictResponse;
use Mindee\Product\Invoice\InvoiceV4;
use Mindee\Product\Invoice\InvoiceV4Document;
use PHPUnit\Framework\TestCase;

class PredictResponseTest extends TestCase
{
    public function testLoadingFromJSONShouldCreateAPredictResponse()
    {
        $json = file_get_contents(
            (
                getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/products/invoices/response_v4/complete.json"
        );
        $response = json_decode($json, true);
        $parsedResponse = new PredictResponse(InvoiceV4::class, $response);
        $this->assertInstanceOf(InvoiceV4::class, $parsedResponse->document->inference);
        foreach ($parsedResponse->document->inference->pages as $page) {
            $this->assertInstanceOf(InvoiceV4Document::class, $page->prediction);
        }
        $this->assertEquals(2, $parsedResponse->document->nPages);
    }
}
