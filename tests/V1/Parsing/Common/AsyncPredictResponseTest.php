<?php

namespace V1\Parsing\Common;

use Mindee\Http\ResponseValidation;
use Mindee\Parsing\Common\AsyncPredictResponse;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;
use PHPUnit\Framework\TestCase;

class AsyncPredictResponseTest extends TestCase
{
    private string $filePathPostSuccess;
    private string $filePathPostFail;
    private string $filePathGetFailJob;
    private string $filePathGetProcessing;
    private string $filePathGetCompleted;

    protected function setUp(): void
    {
        $asyncDir = \TestingUtilities::getV1DataDir() . "/async";
        $this->filePathPostSuccess = $asyncDir . "/post_success.json";
        $this->filePathPostFail = $asyncDir . "/post_fail_forbidden.json";
        $this->filePathGetProcessing = $asyncDir . "/get_processing.json";
        $this->filePathGetCompleted = $asyncDir . "/get_completed.json";
        $this->filePathGetFailJob = $asyncDir . "/get_failed_job_error.json";
    }

    public function testAsyncResponsePOSTSuccess()
    {
        $json = file_get_contents($this->filePathPostSuccess);
        $response = json_decode($json, true);
        $this->assertTrue(ResponseValidation::isValidAsyncResponse(["data" => $response, "code" => 200]));
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
        $json = file_get_contents($this->filePathPostFail);
        $response = json_decode($json, true);
        $this->assertFalse(ResponseValidation::isValidAsyncResponse(["data" => $response, "code" => 200]));
    }
    public function testAsyncResponseGETFailedJob()
    {
        $json = file_get_contents($this->filePathGetFailJob);
        $response = json_decode($json, true);
        $this->assertFalse(ResponseValidation::isValidAsyncResponse(["data" => $response, "code" => 200]));
    }

    public function testAsyncResponseGETProcessing()
    {
        $json = file_get_contents($this->filePathGetProcessing);
        $response = json_decode($json, true);
        $this->assertTrue(ResponseValidation::isValidAsyncResponse(["data" => $response, "code" => 200]));
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
        $json = file_get_contents($this->filePathGetCompleted);
        $response = json_decode($json, true);
        $this->assertTrue(ResponseValidation::isValidAsyncResponse(["data" => $response, "code" => 200]));
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
