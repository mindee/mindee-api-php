<?php

declare(strict_types=1);

namespace V1\Extraction;

use Mindee\Extraction\PDFExtractor;
use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\V1\Client;
use Mindee\V1\Product\InvoiceSplitter\InvoiceSplitterV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class PDFExtractorTest extends TestCase
{
    private Client $dummyClient;

    protected function setUp(): void
    {
        $this->dummyClient = new Client("dummy-key");
    }
    public function testGivenAnImageShouldExtractAPDF(): void
    {
        $jpg = TestingUtilities::getV1DataDir() . "/products/invoices/default_sample.jpg";
        $localInput = new PathInput($jpg);
        self::assertFalse($localInput->isPDF());
        $extractor = new PDFExtractor($localInput);
        self::assertSame(1, $extractor->getPageCount());
    }

    /**
     *
     */
    public function testGivenAPDFShouldExtractInvoicesNoStrict(): void
    {
        $pdf = new PathInput(TestingUtilities::getV1DataDir() . "/products/invoice_splitter/invoice_5p.pdf");
        $response = $this->getPrediction();
        self::assertNotNull($response);
        $inference = $response->document->inference;
        $extractor = new PDFExtractor($pdf);
        self::assertSame(5, $extractor->getPageCount());

        $extractedPDFSNoStrict = $extractor->extractInvoices($inference->prediction->invoicePageGroups);
        self::assertCount(3, $extractedPDFSNoStrict);
        self::assertSame("invoice_5p_001-001.pdf", $extractedPDFSNoStrict[0]->getFileName());
        self::assertSame(1, $extractedPDFSNoStrict[0]->getPageCount());
        self::assertSame("invoice_5p_002-004.pdf", $extractedPDFSNoStrict[1]->getFileName());
        self::assertSame(3, $extractedPDFSNoStrict[1]->getPageCount());
        self::assertSame("invoice_5p_005-005.pdf", $extractedPDFSNoStrict[2]->getFileName());
        self::assertSame(1, $extractedPDFSNoStrict[2]->getPageCount());
    }

    /**
     *
     */
    public function testGivenAPDFShouldExtractInvoicesStrict(): void
    {
        $pdf = new PathInput(TestingUtilities::getV1DataDir() . "/products/invoice_splitter/invoice_5p.pdf");
        $response = $this->getPrediction();
        self::assertNotNull($response);
        $inference = $response->document->inference;

        $extractor = new PDFExtractor($pdf);
        self::assertSame(5, $extractor->getPageCount());

        $extractedPDFStrict = $extractor->extractInvoices($inference->prediction->invoicePageGroups, true);
        self::assertCount(2, $extractedPDFStrict);
        self::assertSame("invoice_5p_001-001.pdf", $extractedPDFStrict[0]->getFileName());
        self::assertSame(1, $extractedPDFStrict[0]->getPageCount());
        self::assertSame("invoice_5p_002-005.pdf", $extractedPDFStrict[1]->getFileName());
        self::assertSame(4, $extractedPDFStrict[1]->getPageCount());
    }

    private function getPrediction()
    {
        $fileName = TestingUtilities::getV1DataDir() . "/products/invoice_splitter/response_v1/complete.json";
        $localResponse = new LocalResponse($fileName);
        return $this->dummyClient->loadPrediction(InvoiceSplitterV1::class, $localResponse);
    }
}
