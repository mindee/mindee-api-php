<?php

namespace Input;
use Mindee\Client;
use Mindee\Input\URLInputSource;
use PHPUnit\Framework\TestCase;

class URLInputSourceTest extends TestCase
{

    protected Client $dummyClient;
    protected string $fileTypesDir;

    public function setUp(): void
    {
        $this->dummyClient = new Client("dummy-key");
        putenv('MINDEE_API_KEY' . '=');
        $this->fileTypesDir = (
            getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/file_types/";
    }
    public function testInputFromHTTPShouldNotThrow()
    {
        $inputDoc = $this->dummyClient->sourceFromUrl("https://example.com/invoice.pdf");
        $this->assertInstanceOf(URLInputSource::class, $inputDoc);
    }
}
