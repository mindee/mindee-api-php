<?php

declare(strict_types=1);

namespace Input;

use Mindee\Error\MindeeSourceException;
use Mindee\Input\UrlInputSource;
use Mindee\V1\Client;
use PHPUnit\Framework\TestCase;

use const Mindee\V1\Http\API_KEY_ENV_NAME;

class UrlInputSourceTest extends TestCase
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

    public function testInputFromHttpShouldNotThrow(): void
    {
        $inputDoc = new UrlInputSource("https://example.com/invoice.pdf");
        self::assertInstanceOf(UrlInputSource::class, $inputDoc);
    }

    public function testInputFromHttpShouldThrow(): void
    {
        $this->expectException(MindeeSourceException::class);
        new UrlInputSource(url: "http://example.com/invoice.pdf");
    }

    public function testRejectsEmbeddedCredentials(): void
    {
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage('URL must not embed user credentials');
        new UrlInputSource('https://user:pass@example.com/invoice.pdf');
    }

    public function testRejectsLocalhostHostname(): void
    {
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage('URL host is a loopback address');
        new UrlInputSource('https://localhost/invoice.pdf');
    }

    public function testRejectsDotLocalhostHostname(): void
    {
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage('URL host is a loopback address');
        new UrlInputSource('https://foo.localhost/invoice.pdf');
    }

    public function testRejectsLoopbackIpv4(): void
    {
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage('URL host is a loopback or private address');
        new UrlInputSource('https://127.0.0.1/invoice.pdf');
    }

    public function testRejectsLoopbackIpv6(): void
    {
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage('URL host is a loopback or private address');
        new UrlInputSource('https://[::1]/invoice.pdf');
    }

    public function testRejectsPrivateRfc1918Class10(): void
    {
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage('URL host is a loopback or private address');
        new UrlInputSource('https://10.0.0.1/invoice.pdf');
    }

    public function testRejectsPrivateRfc1918Class172(): void
    {
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage('URL host is a loopback or private address');
        new UrlInputSource('https://172.16.0.1/invoice.pdf');
    }

    public function testRejectsPrivateRfc1918Class192(): void
    {
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage('URL host is a loopback or private address');
        new UrlInputSource('https://192.168.1.1/invoice.pdf');
    }

    public function testRejectsLinkLocalAddress(): void
    {
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage('URL host is a loopback or private address');
        new UrlInputSource('https://169.254.0.1/invoice.pdf');
    }

    public function testDownloadFileFails(): void
    {
        $dummyAddress = "addressthatdoesntworkforcipurposes";
        $urlSource = new UrlInputSource("https://$dummyAddress");
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage("Failed to download file: Could not resolve host: $dummyAddress");
        $urlSource->asLocalInputSource("test.pdf");
    }

    public function testInvalidFileName(): void
    {
        $urlSource = new UrlInputSource("https://addressthatdoesntworkforcipurposes");
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage("Filename must end with an extension.");
        $urlSource->asLocalInputSource("toto");
    }
}
