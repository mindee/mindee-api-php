<?php

declare(strict_types=1);

namespace V1\Error;

use Mindee\Error\V1\MindeeV1HttpException;
use Mindee\Input\PathInput;
use Mindee\V1\Client;
use Mindee\V1\Product\Invoice\InvoiceV4;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class MindeeHttpExceptionTest extends TestCase
{
    private string $errorDir;
    private PathInput $dummyFile;
    private Client $dummyClient;

    protected function setUp(): void
    {
        $this->errorDir = TestingUtilities::getV1DataDir() . "/errors/";
        $this->dummyClient = new Client("dummy-key");
        $this->dummyFile = new PathInput(
            TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf"
        );
    }

    public function testHttpClientErrorShouldRaise(): void
    {
        $this->expectException(MindeeV1HttpException::class);
        $this->dummyClient->parse(InvoiceV4::class, $this->dummyFile);
    }

    public function testHttpEnqueueClientException(): void
    {
        $this->expectException(MindeeV1HttpException::class);
        $this->dummyClient->enqueue(InvoiceV4::class, $this->dummyFile);
    }

    public function testHttpParseClientException(): void
    {
        $this->expectException(MindeeV1HttpException::class);
        $this->dummyClient->enqueue(InvoiceV4::class, $this->dummyFile);
    }

    public function testHttpEnqueueAndParseClientException(): void
    {
        $this->expectException(MindeeV1HttpException::class);
        $this->dummyClient->enqueueAndParse(InvoiceV4::class, $this->dummyFile);
    }

    public function testHttp400Exception(): void
    {
        $json = file_get_contents($this->errorDir . "error_400_no_details.json");
        $errorObj = ["data" => json_decode($json, true), "code" => 400];
        $error400 = MindeeV1HttpException::handleError("dummy-url", $errorObj);
        self::assertSame(400, $error400->statusCode);
        self::assertSame("SomeCode", $error400->apiCode);
        self::assertSame("Some scary message here", $error400->apiMessage);
        self::assertNull($error400->apiDetails);
        $this->expectException(MindeeV1HttpException::class);
        throw $error400;
    }

    public function testHttp401Exception(): void
    {
        $json = file_get_contents($this->errorDir . "error_401_invalid_token.json");
        $errorObj = ["data" => json_decode($json, true), "code" => 401];
        $error401 = MindeeV1HttpException::handleError("dummy-url", $errorObj);
        self::assertSame(401, $error401->statusCode);
        self::assertSame("Unauthorized", $error401->apiCode);
        self::assertSame("Authorization required", $error401->apiMessage);
        self::assertSame("Invalid token provided", $error401->apiDetails);
        $this->expectException(MindeeV1HttpException::class);
        throw $error401;
    }

    public function testHttp429Exception(): void
    {
        $json = file_get_contents($this->errorDir . "error_429_too_many_requests.json");
        $errorObj = ["data" => json_decode($json, true), "code" => 429];
        $error429 = MindeeV1HttpException::handleError("dummy-url", $errorObj);
        self::assertSame(429, $error429->statusCode);
        self::assertSame("TooManyRequests", $error429->apiCode);
        self::assertSame("Too many requests", $error429->apiMessage);
        self::assertSame("Too Many Requests.", $error429->apiDetails);
        $this->expectException(MindeeV1HttpException::class);
        throw $error429;
    }

    public function testHttp500Exception(): void
    {
        $json = file_get_contents($this->errorDir . "error_500_inference_fail.json");
        $errorObj = ["data" => json_decode($json, true), "code" => 500];
        $error500 = MindeeV1HttpException::handleError("dummy-url", $errorObj);
        self::assertSame(500, $error500->statusCode);
        self::assertSame("failure", $error500->apiCode);
        self::assertSame("Inference failed", $error500->apiMessage);
        self::assertSame("Can not run prediction: ", $error500->apiDetails);
        $this->expectException(MindeeV1HttpException::class);
        throw $error500;
    }

    public function testHttp500HTMLError(): void
    {
        $errorRefContents = file_get_contents($this->errorDir . "error_50x.html");
        $error500 = MindeeV1HttpException::handleError("dummy-url", ["data" => $errorRefContents, "code" => 500]);
        self::assertSame(500, $error500->statusCode);
        self::assertSame("UnknownError", $error500->apiCode);
        self::assertSame("Server sent back an unexpected reply.", $error500->apiMessage);
        self::assertSame($errorRefContents, $error500->apiDetails);
        $this->expectException(MindeeV1HttpException::class);
        throw $error500;
    }
}
