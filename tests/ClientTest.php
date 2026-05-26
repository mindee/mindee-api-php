<?php

declare(strict_types=1);

use Mindee\ClientOptions\PollingOptions;
use Mindee\Error\MindeeAPIException;
use Mindee\Error\MindeeHttpClientException;
use Mindee\Error\MindeeHttpException;
use Mindee\Input\LocalResponse;
use Mindee\Input\PageOptions;
use Mindee\V1\Client;
use Mindee\V1\ClientOptions\PredictMethodOptions;
use Mindee\V1\Product\Generated\GeneratedV1;
use Mindee\V1\Product\Invoice\InvoiceV4;
use Mindee\V1\Product\InvoiceSplitter\InvoiceSplitterV1;
use Mindee\V1\Product\MultiReceiptsDetector\MultiReceiptsDetectorV1;
use Mindee\V1\Product\Receipt\ReceiptV5;
use PHPUnit\Framework\TestCase;
use Mindee\Error\MindeeMimeTypeException;

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
            TestingUtilities::getV1DataDir() . "/products/multi_receipts_detector/response_v1/complete.json"
        );
        $this->failedJobPath = (
            TestingUtilities::getV1DataDir() . "/async/get_failed_job_error.json"
        );
    }


    protected function tearDown(): void
    {
        putenv('MINDEE_API_KEY=' . $this->oldKey);
    }

    public function testParsePathWithoutToken(): void
    {
        $this->expectException(MindeeHttpClientException::class);

        $inputDoc = $this->emptyClient->sourceFromPath(TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        $this->emptyClient->parse(InvoiceV4::class, $inputDoc);
    }

    public function testParsePathWithEnvToken(): void
    {
        $this->expectException(MindeeHttpException::class);

        $inputDoc = $this->envClient->sourceFromPath(TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        $this->envClient->parse(InvoiceV4::class, $inputDoc);
    }

    public function testParsePathWithWrongFileType(): void
    {
        $this->expectException(MindeeMimeTypeException::class);

        $inputDoc = $this->dummyClient->sourceFromPath(TestingUtilities::getFileTypesDir() . "/receipt.txt");
    }

    public function testParsePathWithWrongToken(): void
    {
        $this->expectException(MindeeHttpClientException::class);

        $inputDoc = $this->dummyClient->sourceFromPath(TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        $this->dummyClient->parse(InvoiceV4::class, $inputDoc);
    }

    public function testInterfaceVersion(): void
    {
        $dummyEndpoint = $this->dummyClient->createEndpoint("dummy", "dummy", "1.1");
        $inputDoc = $this->dummyClient->sourceFromPath(TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        $predictOptions = new PredictMethodOptions();
        self::assertSame("1.1", $dummyEndpoint->settings->version);

        $this->expectException(MindeeHttpClientException::class);
        $this->dummyClient->parse(
            GeneratedV1::class,
            $inputDoc,
            $predictOptions->setEndpoint($dummyEndpoint),
        );
    }

    public function testCutOptions(): void
    {
        $inputDoc = $this->dummyClient->sourceFromPath(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $this->expectException(MindeeHttpClientException::class);
        $pageOptions = new PageOptions(range(0, 4));
        $this->dummyClient->parse(ReceiptV5::class, $inputDoc, null, $pageOptions);
        self::assertSame(5, $inputDoc->getPageCount());
    }

    public function testAsyncWrongInitialDelay(): void
    {
        $this->expectException(MindeeAPIException::class);
        $asyncParseOptions = new PollingOptions();
        $asyncParseOptions->setInitialDelaySec(0);
    }

    public function testAsyncWrongPollingDelay(): void
    {
        $this->expectException(MindeeAPIException::class);
        $asyncParseOptions = new PollingOptions();
        $asyncParseOptions->setDelaySec(0);
    }

    public function testPredictOptionsWrongInputType(): void
    {
        $pageOptions = new PageOptions([0, 1]);
        self::assertFalse($pageOptions->isEmpty());
        $predictOptions = new PredictMethodOptions();
        $predictOptions->setPageOptions($pageOptions);
        $urlInputSource = $this->dummyClient->sourceFromUrl("https://dummy");
        $this->expectException(MindeeAPIException::class);
        $this->dummyClient->parse(InvoiceV4::class, $urlInputSource, $predictOptions);
        $this->expectException(MindeeAPIException::class);
        $this->dummyClient->enqueue(InvoiceSplitterV1::class, $urlInputSource, $predictOptions);
    }

    public function testPredictOptionsValidInputType(): void
    {
        $predictOptions = new PredictMethodOptions();
        self::assertTrue($predictOptions->pageOptions->isEmpty());
        $inputDoc = $this->dummyClient->sourceFromPath(TestingUtilities::getFileTypesDir() . "/pdf/blank.pdf");
        $this->expectException(MindeeHttpClientException::class);
        $this->dummyClient->parse(InvoiceV4::class, $inputDoc, $predictOptions);
        $this->expectException(MindeeHttpClientException::class);
        $this->dummyClient->enqueue(InvoiceSplitterV1::class, $inputDoc, $predictOptions);
    }

    public function testLoadLocalResponse(): void
    {
        $localResponse = new LocalResponse($this->multiReceiptsDetectorPath);
        $res = $this->dummyClient->loadPrediction(MultiReceiptsDetectorV1::class, $localResponse);
        self::assertNotNull($res);
        self::assertSame(1, $res->document->nPages);
    }

    public function testLoadFailedLocalResponse(): void
    {
        $localResponse = new LocalResponse($this->failedJobPath);
        $res = $this->dummyClient->loadPrediction(InvoiceV4::class, $localResponse);
        self::assertNotNull($res);
        self::assertSame("failed", $res->job->status);
    }
}
