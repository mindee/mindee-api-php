<?php

declare(strict_types=1);

namespace V1\Http;

use Mindee\Error\MindeeException;
use Mindee\V1\HTTP\MindeeAPI;
use Mindee\V1\Product\InvoiceSplitter\InvoiceSplitterV1;
use PHPUnit\Framework\TestCase;
use Mindee\V1\Client;

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

    public function testGivenOTSParametersAProperMindeeApiObjectShouldBeCreated(): void
    {
        $settings = new MindeeAPI("my-api-key", InvoiceSplitterV1::$endpointName);
        self::assertSame("my-api-key", $settings->apiKey);
        self::assertSame(InvoiceSplitterV1::$endpointName, $settings->endpointName);
        self::assertSame(Client::DEFAULT_OWNER, $settings->accountName);
        self::assertSame("1", $settings->version);
    }

    public function testGivenCustomParametersAProperMindeeApiObjectShouldBeCreated(): void
    {
        $settings = new MindeeAPI("my-api-key", "custom-endpoint-name", "custom-owner-name", "1.3");
        self::assertSame("my-api-key", $settings->apiKey);
        self::assertSame("custom-endpoint-name", $settings->endpointName);
        self::assertSame("custom-owner-name", $settings->accountName);
        self::assertSame("1.3", $settings->version);
    }

    public function testGivenInvalidApiKeyAnExceptionShouldBeThrown(): void
    {
        $this->expectException(MindeeException::class);
        putenv(API_KEY_ENV_NAME . '=');
        new MindeeAPI(null, InvoiceSplitterV1::$endpointName);
    }
}
