<?php

namespace Error;

use Mindee\Client;
use Mindee\Error\MindeeHttpClientException;
use Mindee\Error\MindeeHttpException;
use Mindee\Input\PathInput;
use Mindee\Product\Invoice\InvoiceV4;
use PHPUnit\Framework\TestCase;

class MindeeHttpExceptionTest extends TestCase
{
    private string $errorDir;
    private PathInput $dummyFile;
    private Client $dummyClient;

    protected function setUp(): void
    {
        $this->errorDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/errors/";
        $this->dummyClient = new Client("dummy-key");
        $this->dummyFile = $this->dummyClient->sourceFromPath(
            (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/file_types/pdf/blank.pdf"
        );
    }

    public function testHTTPClientErrorShouldRaise()
    {
        $this->expectException(MindeeHttpClientException::class);
        $this->dummyClient->parse(InvoiceV4::class, $this->dummyFile);
    }

    public function testHTTPEnqueueClientException()
    {
        $this->expectException(MindeeHttpClientException::class);
        $this->dummyClient->enqueue(InvoiceV4::class, $this->dummyFile);
    }

    public function testHTTPParseClientException()
    {
        $this->expectException(MindeeHttpClientException::class);
        $this->dummyClient->enqueue(InvoiceV4::class, $this->dummyFile);
    }

    public function testHTTPEnqueueAndParseClientException()
    {
        $this->expectException(MindeeHttpClientException::class);
        $this->dummyClient->enqueueAndParse(InvoiceV4::class, $this->dummyFile);
    }

    public function testHTTP400Exception()
    {
        $json = file_get_contents($this->errorDir . "error_400_no_details.json");
        $errorObj = ["data" => json_decode($json, true), "code" => 400];
        $error400 = MindeeHttpException::handleError("dummy-url", $errorObj);
        $this->assertEquals(400, $error400->statusCode);
        $this->assertEquals("SomeCode", $error400->apiCode);
        $this->assertEquals("Some scary message here", $error400->apiMessage);
        $this->assertNull($error400->apiDetails);
        $this->expectException(MindeeHttpClientException::class);
        throw $error400;
    }

    public function testHTTP401Exception()
    {
        $json = file_get_contents($this->errorDir . "error_401_invalid_token.json");
        $errorObj = ["data" => json_decode($json, true), "code" => 401];
        $error401 = MindeeHttpException::handleError("dummy-url", $errorObj);
        $this->assertEquals(401, $error401->statusCode);
        $this->assertEquals("Unauthorized", $error401->apiCode);
        $this->assertEquals("Authorization required", $error401->apiMessage);
        $this->assertEquals("Invalid token provided", $error401->apiDetails);
        $this->expectException(MindeeHttpClientException::class);
        throw $error401;
    }

    public function testHTTP429Exception()
    {
        $json = file_get_contents($this->errorDir . "error_429_too_many_requests.json");
        $errorObj = ["data" => json_decode($json, true), "code" => 429];
        $error429 = MindeeHttpException::handleError("dummy-url", $errorObj);
        $this->assertEquals(429, $error429->statusCode);
        $this->assertEquals("TooManyRequests", $error429->apiCode);
        $this->assertEquals("Too many requests", $error429->apiMessage);
        $this->assertEquals("Too Many Requests.", $error429->apiDetails);
        $this->expectException(MindeeHttpClientException::class);
        throw $error429;
    }

    public function testHTTP500Exception()
    {
        $json = file_get_contents($this->errorDir . "error_500_inference_fail.json");
        $errorObj = ["data" => json_decode($json, true), "code" => 500];
        $error500 = MindeeHttpException::handleError("dummy-url", $errorObj);
        $this->assertEquals(500, $error500->statusCode);
        $this->assertEquals("failure", $error500->apiCode);
        $this->assertEquals("Inference failed", $error500->apiMessage);
        $this->assertEquals("Cannot run prediction: ", $error500->apiDetails);
        $this->expectException(MindeeHttpClientException::class);
        throw $error500;
    }

    public function testHTTP500HTMLError()
    {
        $errorRefContents = file_get_contents($this->errorDir . "error_50x.html");
        $error500 = MindeeHttpException::handleError("dummy-url", ["data" => $errorRefContents, "code" => 500]);
        $this->assertEquals(500, $error500->statusCode);
        $this->assertEquals("UnknownError", $error500->apiCode);
        $this->assertEquals("Server sent back an unexpected reply.", $error500->apiMessage);
        $this->assertEquals($errorRefContents, $error500->apiDetails);
        $this->expectException(MindeeHttpClientException::class);
        throw $error500;
    }
}
