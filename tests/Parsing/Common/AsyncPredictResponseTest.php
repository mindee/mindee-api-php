<?php

namespace Parsing\Common;

use Mindee\Input\PathInput;
use Mindee\Parsing\Common\AsyncPredictResponse;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;
use PHPUnit\Framework\TestCase;

const ASYNC_DIR = "./tests/resources/async";

const FILE_PATH_POST_SUCCESS = ASYNC_DIR . "/post_success.json";
const FILE_PATH_POST_FAIL = ASYNC_DIR . "/post_fail_forbidden.json";
const FILE_PATH_GET_PROCESSING = ASYNC_DIR . "/get_processing.json";
const FILE_PATH_GET_COMPLETED = ASYNC_DIR . "/get_completed.json";


class AsyncPredictResponseTest extends TestCase
{
    public PathInput $fileInput;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->fileInput = new PathInput("./tests/resources/products/invoice_splitter/default_sample.pdf");
    }

    public function testAsyncResponsePOSTSuccess()
    {
        $json = file_get_contents(FILE_PATH_POST_SUCCESS);
        $response = json_decode($json, true);
        $parsedResponse = new AsyncPredictResponse(InvoiceSplitterV1::class, $response);
        $this->assertNotNull($parsedResponse->job);
        $this->assertEquals(
            "2023-02-16T12:33:49.602947+00:00",
            $parsedResponse->job->issuedAt->format('Y-m-d\TH:i:s.uP')
        );
        $this->assertNull($parsedResponse->job->availableAt);
        $this->assertEquals("waiting", $parsedResponse->job->status);
        $this->assertEquals("76c90710-3a1b-4b91-8a39-31a6543e347c", $parsedResponse->job->id);
        $this->assertEmpty($parsedResponse->apiRequest->error);
    }

    public function testAsyncResponsePOSTFail()
    {
        $json = file_get_contents(FILE_PATH_POST_FAIL);
        $response = json_decode($json, true);
        $parsedResponse = new AsyncPredictResponse(InvoiceSplitterV1::class, $response);
        $this->assertNotNull($parsedResponse->job);
        $this->assertEquals(
            "2023-01-01T00:00:00+00:00",
            $parsedResponse->job->issuedAt->format('Y-m-d\TH:i:sP')
        );
        $this->assertNull($parsedResponse->job->availableAt);
        $this->assertNull($parsedResponse->job->status);
        $this->assertNull($parsedResponse->job->id);
        $this->assertNull($parsedResponse->job->id);
        $this->assertEquals("Forbidden", $parsedResponse->apiRequest->error["code"]);
    }

    public function testAsyncResponseGETProcessing()
    {
        $json = file_get_contents(FILE_PATH_GET_PROCESSING);
        $response = json_decode($json, true);
        $parsedResponse = new AsyncPredictResponse(InvoiceSplitterV1::class, $response);
        $this->assertNotNull($parsedResponse->job);
        $this->assertEquals(
            "2023-03-16T12:33:49.602947",
            $parsedResponse->job->issuedAt->format('Y-m-d\TH:i:s.u')
        );
        $this->assertNull($parsedResponse->job->availableAt);
        $this->assertEquals("processing", $parsedResponse->job->status);
        $this->assertEquals("76c90710-3a1b-4b91-8a39-31a6543e347c", $parsedResponse->job->id);
        $this->assertEmpty($parsedResponse->apiRequest->error);
    }

    public function testAsyncResponseGETCompleted()
    {
        $json = file_get_contents(FILE_PATH_GET_COMPLETED);
        $response = json_decode($json, true);
        $parsedResponse = new AsyncPredictResponse(InvoiceSplitterV1::class, $response);
        $this->assertNotNull($parsedResponse->job);
        $this->assertEquals(
            "2023-03-21T13:52:56.326107",
            $parsedResponse->job->issuedAt->format('Y-m-d\TH:i:s.u')
        );
        $this->assertEquals(
            "2023-03-21T13:53:00.990339",
            $parsedResponse->job->availableAt->format('Y-m-d\TH:i:s.u')
        );
        $this->assertEquals("completed", $parsedResponse->job->status);
        $this->assertEquals("b6caf9e8-9bcc-4412-bcb7-f5b416678f0d", $parsedResponse->job->id);
        $this->assertEmpty($parsedResponse->apiRequest->error);
    }
}
