<?php

namespace Input;

use Mindee\Client;
use Mindee\Error\MindeeSourceException;
use Mindee\Input\URLInputSource;
use PHPUnit\Framework\TestCase;

use const Mindee\Http\API_KEY_ENV_NAME;

class URLInputSourceTest extends TestCase
{
    private string $oldKey;
    protected Client $dummyClient;
    protected string $fileTypesDir;

    protected function setUp(): void
    {
        $this->oldKey = getEnv(API_KEY_ENV_NAME);
        $this->dummyClient = new Client("dummy-key");
        $this->fileTypesDir = (
            getenv('GITHUB_WORKSPACE') ?: "."
        ) . "/tests/resources/file_types/";
    }
    protected function tearDown(): void
    {
        putenv('MINDEE_API_KEY=' . $this->oldKey);
    }
    public function testInputFromHTTPShouldNotThrow()
    {
        $inputDoc = $this->dummyClient->sourceFromUrl("https://example.com/invoice.pdf");
        $this->assertInstanceOf(URLInputSource::class, $inputDoc);
    }
    public function testInputFromHTTPShouldThrow()
    {
        $this->expectException(MindeeSourceException::class);
        $this->dummyClient->sourceFromUrl("http://example.com/invoice.pdf");
    }
    public function testDownloadFileFails(){
        $dummyAddress = "addressthatdoesntworkforcipurposes";
        $urlSource = $this->dummyClient->sourceFromUrl("https://$dummyAddress");
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage("Failed to download file: Could not resolve host: $dummyAddress");
        $urlSource->asLocalInputSource("test.pdf");
    }
    public function testInvalidFileName(){
        $urlSource = $this->dummyClient->sourceFromUrl("https://addressthatdoesntworkforcipurposes");
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage("Filename must end with an extension.");
        $urlSource->asLocalInputSource("toto");
    }
}
