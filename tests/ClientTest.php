<?php

use Mindee\Client;
use Mindee\Error\MindeeApiException;
use Mindee\Error\MindeeHttpClientException;
use Mindee\Error\MindeeHttpException;
use Mindee\Input\LocalResponse;
use Mindee\Input\PageOptions;
use Mindee\Input\PollingOptions;
use Mindee\Input\PredictMethodOptions;
use Mindee\Product\Generated\GeneratedV1;
use Mindee\Product\Invoice\InvoiceV4;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;
use Mindee\Product\MultiReceiptsDetector\MultiReceiptsDetectorV1;
use Mindee\Product\Receipt\ReceiptV5;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private Client $emptyClient;
    private Client $dummyClient;
    private Client $envClient;
    private string $oldKey;
    private string $multiReceiptsDetectorPath;
    private string $failedJobPath;


    protected function setUp(): void
    {
        $this->oldKey = getenv('MINDEE_API_KEY');
        $this->dummyClient = new Client("dummy-key");
        putenv('MINDEE_API_KEY=');
        $this->emptyClient = new Client();
        putenv('MINDEE_API_KEY=dummy-env-key');
        $this->envClient = new Client();
        $this->multiReceiptsDetectorPath = (
            \TestingUtilities::getV1DataDir() . "/products/multi_receipts_detector/response_v1/complete.json"
        );
        $this->failedJobPath = (
            \TestingUtilities::getV1DataDir() . "/async/get_failed_job_error.json"
        );
    }


    protected function tearDown(): void
    {
        putenv('MINDEE_API_KEY=' . $this->oldKey);
    }

    public function testParsePathWithoutToken()
    {
        $this->expectException(MindeeHttpClientException::class);

        $inputDoc = $this->emptyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        $this->emptyClient->parse(InvoiceV4::class, $inputDoc);
    }

    public function testParsePathWithEnvToken()
    {
        $this->expectException(MindeeHttpException::class);

        $inputDoc = $this->envClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        $this->envClient->parse(InvoiceV4::class, $inputDoc);
    }

    public function testParsePathWithWrongFileType()
    {
        $this->expectException(Mindee\Error\MindeeMimeTypeException::class);

        $inputDoc = $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() ."/receipt.txt");
    }

    public function testParsePathWithWrongToken()
    {
        $this->expectException(MindeeHttpClientException::class);

        $inputDoc = $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        $this->dummyClient->parse(InvoiceV4::class, $inputDoc);
    }

    public function testInterfaceVersion()
    {
        $dummyEndpoint = $this->dummyClient->createEndpoint("dummy", "dummy", "1.1");
        $inputDoc = $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        $predictOptions = new PredictMethodOptions();
        $this->assertEquals("1.1", $dummyEndpoint->settings->version);

        $this->expectException(MindeeHTTPClientException::class);
        $this->dummyClient->parse(
            GeneratedV1::class,
            $inputDoc,
            $predictOptions->setEndpoint($dummyEndpoint),
        );
    }

    public function testCutOptions()
    {
        $inputDoc = $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $this->expectException(MindeeHttpClientException::class);
        $pageOptions = new PageOptions(range(0, 4));
        $this->dummyClient->parse(ReceiptV5::class, $inputDoc, null, $pageOptions);
        $this->assertEquals(5, $inputDoc->getPageCount());
    }

    public function testAsyncWrongInitialDelay()
    {
        $this->expectException(MindeeApiException::class);
        $asyncParseOptions = new PollingOptions();
        $asyncParseOptions->setInitialDelaySec(0);
    }

    public function testAsyncWrongPollingDelay()
    {
        $this->expectException(MindeeApiException::class);
        $asyncParseOptions = new PollingOptions();
        $asyncParseOptions->setDelaySec(0);
    }

    public function testPredictOptionsWrongInputType()
    {
        $pageOptions = new PageOptions([0, 1]);
        $this->assertFalse($pageOptions->isEmpty());
        $predictOptions = new PredictMethodOptions();
        $predictOptions->setPageOptions($pageOptions);
        $urlInputSource = $this->dummyClient->sourceFromUrl("https://dummy");
        $this->expectException(MindeeApiException::class);
        $this->dummyClient->parse(InvoiceV4::class, $urlInputSource, $predictOptions);
        $this->expectException(MindeeApiException::class);
        $this->dummyClient->enqueue(InvoiceSplitterV1::class, $urlInputSource, $predictOptions);
    }

    public function testPredictOptionsValidInputType()
    {
        $predictOptions = new PredictMethodOptions();
        $this->assertTrue($predictOptions->pageOptions->isEmpty());
        $inputDoc = $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        $this->expectException(MindeeHttpClientException::class);
        $this->dummyClient->parse(InvoiceV4::class, $inputDoc, $predictOptions);
        $this->expectException(MindeeHttpClientException::class);
        $this->dummyClient->enqueue(InvoiceSplitterV1::class, $inputDoc, $predictOptions);
    }

    public function testLoadLocalResponse()
    {
        $localResponse = new LocalResponse($this->multiReceiptsDetectorPath);
        $res = $this->dummyClient->loadPrediction(MultiReceiptsDetectorV1::class, $localResponse);
        $this->assertNotNull($res);
        $this->assertEquals(1, $res->document->nPages);
    }

    public function testLoadFailedLocalResponse()
    {
        $localResponse = new LocalResponse($this->failedJobPath);
        $res = $this->dummyClient->loadPrediction(InvoiceV4::class, $localResponse);
        $this->assertNotNull($res);
        $this->assertEquals("failed", $res->job->status);
    }
}
