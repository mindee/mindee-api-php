<?php

namespace Http;

use Mindee\Error\MindeeException;
use Mindee\Http\MindeeApi;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;
use PHPUnit\Framework\TestCase;

use const Mindee\Http\API_KEY_ENV_NAME;

class MindeeApiTest extends TestCase
{
    public function testGivenOTSParametersAProperMindeeApiObjectShouldBeCreated()
    {
        $settings = new MindeeApi("my-api-key", InvoiceSplitterV1::$endpointName);
        $this->assertEquals("my-api-key", $settings->apiKey);
        $this->assertEquals(InvoiceSplitterV1::$endpointName, $settings->endpointName);
        $this->assertEquals(\Mindee\Client::DEFAULT_OWNER, $settings->accountName);
        $this->assertEquals("1", $settings->version);
    }

    public function testGivenCustomParametersAProperMindeeApiObjectShouldBeCreated()
    {
        $settings = new MindeeApi("my-api-key", "custom-endpoint-name", "custom-owner-name", "1.3");
        $this->assertEquals("my-api-key", $settings->apiKey);
        $this->assertEquals("custom-endpoint-name", $settings->endpointName);
        $this->assertEquals("custom-owner-name", $settings->accountName);
        $this->assertEquals("1.3", $settings->version);
    }

    public function testGivenInvalidApiKeyAnExceptionShouldBeThrown()
    {
        $this->expectException(MindeeException::class);
        putenv(API_KEY_ENV_NAME . '=');
        $settings = new MindeeApi(null, InvoiceSplitterV1::$endpointName);
    }
}
