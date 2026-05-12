<?php

declare(strict_types=1);

namespace Input;

use Mindee\Error\MindeeSourceException;
use Mindee\Input\URLInputSource;
use Mindee\V1\Client;
use PHPUnit\Framework\TestCase;

use const Mindee\V1\HTTP\API_KEY_ENV_NAME;

class URLInputSourceTest extends TestCase
{
    private string $oldKey;
    protected Client $dummyClient;

    protected function setUp(): void
    {
        $this->oldKey = getEnv(API_KEY_ENV_NAME);
        $this->dummyClient = new Client("dummy-key");
    }

    protected function tearDown(): void
    {
        putenv('MINDEE_API_KEY=' . $this->oldKey);
    }

    public function testInputFromHTTPShouldNotThrow(): void
    {
        $inputDoc = $this->dummyClient->sourceFromUrl("https://example.com/invoice.pdf");
        self::assertInstanceOf(URLInputSource::class, $inputDoc);
    }

    public function testInputFromHTTPShouldThrow(): void
    {
        $this->expectException(MindeeSourceException::class);
        new URLInputSource(url: "http://example.com/invoice.pdf");
    }

    public function testDownloadFileFails(): void
    {
        $dummyAddress = "addressthatdoesntworkforcipurposes";
        $urlSource = $this->dummyClient->sourceFromUrl("https://$dummyAddress");
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage("Failed to download file: Could not resolve host: $dummyAddress");
        $urlSource->asLocalInputSource("test.pdf");
    }

    public function testInvalidFileName(): void
    {
        $urlSource = $this->dummyClient->sourceFromUrl("https://addressthatdoesntworkforcipurposes");
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage("Filename must end with an extension.");
        $urlSource->asLocalInputSource("toto");
    }
}
