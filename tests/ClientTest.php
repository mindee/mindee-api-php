<?php

use Mindee\Client;
use Mindee\Error\MindeeApiException;
use Mindee\Error\MindeeHttpClientException;
use Mindee\Error\MindeeHttpException;
use Mindee\Input\EnqueueAndParseMethodOptions;
use Mindee\Input\PredictMethodOptions;
use Mindee\Product\Custom\CustomV1;
use Mindee\Product\Invoice\InvoiceV4;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private Client $emptyClient;
    private Client $dummyClient;
    private Client $envClient;
    private string $fileTypesDir;


    protected function setUp(): void
    {
        $this->dummyClient = new Client("dummy-key");
        putenv('MINDEE_API_KEY' . '=');
        $this->emptyClient = new Client();
        putenv('MINDEE_API_KEY' . '=dummy-env-key');
        $this->envClient = new Client();
        $this->fileTypesDir = (
            getenv('GITHUB_WORKSPACE') ?: "."
            ) . "/tests/resources/file_types/";
    }

    public function testParsePathWithoutToken()
    {
        $this->expectException(MindeeHttpClientException::class);

        $inputDoc = $this->emptyClient->sourceFromPath($this->fileTypesDir . "pdf/blank.pdf");
        $this->emptyClient->parse(InvoiceV4::class, $inputDoc);
    }

    public function testParsePathWithEnvToken()
    {
        $this->expectException(MindeeHttpException::class);

        $inputDoc = $this->envClient->sourceFromPath($this->fileTypesDir . "pdf/blank.pdf");
        $this->envClient->parse(InvoiceV4::class, $inputDoc);
    }

    public function testParsePathWithWrongFileType()
    {
        $this->expectException(Mindee\Error\MindeeMimeTypeException::class);

        $inputDoc = $this->dummyClient->sourceFromPath($this->fileTypesDir . "receipt.txt");
    }

    public function testParsePathWithWrongToken()
    {
        $this->expectException(MindeeHttpClientException::class);

        $inputDoc = $this->dummyClient->sourceFromPath($this->fileTypesDir . "pdf/blank.pdf");
        $this->dummyClient->parse(InvoiceV4::class, $inputDoc);
    }

    public function testInterfaceVersion()
    {
        $dummyEndpoint = $this->dummyClient->createEndpoint("dummy", "dummy", "1.1");
        $inputDoc = $this->dummyClient->sourceFromPath($this->fileTypesDir . "pdf/blank.pdf");
        $predictOptions = new PredictMethodOptions();
        $this->assertEquals("1.1", $dummyEndpoint->settings->version);

        $this->expectException(MindeeHTTPClientException::class);
        $this->dummyClient->parse(CustomV1::class, $inputDoc, $predictOptions->setEndpoint($dummyEndpoint));
    }

    public function testAsyncWrongInitialDelay()
    {
        $this->expectException(MindeeApiException::class);
        $asyncParseOptions = new EnqueueAndParseMethodOptions();
        $asyncParseOptions->setInitialDelaySec(0);
    }

    public function testAsyncWrongPollingDelay()
    {
        $this->expectException(MindeeApiException::class);
        $asyncParseOptions = new EnqueueAndParseMethodOptions();
        $asyncParseOptions->setDelaySec(0);
    }
}
