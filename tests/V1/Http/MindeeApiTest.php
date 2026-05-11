<?php

namespace V1\Http;

use Mindee\Error\MindeeException;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;
use Mindee\V1\HTTP\MindeeAPI;
use PHPUnit\Framework\TestCase;
use const Mindee\V1\HTTP\API_KEY_ENV_NAME;

class MindeeApiTest extends TestCase
{
    private string $keyEnvName;
    protected function setUp(): void
    {
        $this->keyEnvName = getenv(API_KEY_ENV_NAME);
    }

    protected function tearDown(): void
    {
        putenv(API_KEY_ENV_NAME . '=' . $this->keyEnvName);
    }

    public function testGivenOTSParametersAProperMindeeApiObjectShouldBeCreated()
    {
        $settings = new MindeeAPI("my-api-key", InvoiceSplitterV1::$endpointName);
        $this->assertEquals("my-api-key", $settings->apiKey);
        $this->assertEquals(InvoiceSplitterV1::$endpointName, $settings->endpointName);
        $this->assertEquals(\Mindee\V1\Client::DEFAULT_OWNER, $settings->accountName);
        $this->assertEquals("1", $settings->version);
    }

    public function testGivenCustomParametersAProperMindeeApiObjectShouldBeCreated()
    {
        $settings = new MindeeAPI("my-api-key", "custom-endpoint-name", "custom-owner-name", "1.3");
        $this->assertEquals("my-api-key", $settings->apiKey);
        $this->assertEquals("custom-endpoint-name", $settings->endpointName);
        $this->assertEquals("custom-owner-name", $settings->accountName);
        $this->assertEquals("1.3", $settings->version);
    }

    public function testGivenInvalidApiKeyAnExceptionShouldBeThrown()
    {
        $this->expectException(MindeeException::class);
        putenv(API_KEY_ENV_NAME . '=');
        new MindeeAPI(null, InvoiceSplitterV1::$endpointName);
    }
}
