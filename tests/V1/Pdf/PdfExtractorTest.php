<?php

declare(strict_types=1);

namespace V1\Pdf;

use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\V1\Pdf\PdfExtractor;
use Mindee\V1\Client;
use Mindee\V1\Product\InvoiceSplitter\InvoiceSplitterV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class PdfExtractorTest extends TestCase
{
    private Client $dummyClient;

    protected function setUp(): void
    {
        $this->dummyClient = new Client("dummy-key");
    }
    public function testGivenAnImageShouldExtractAPdf(): void
    {
        $jpg = TestingUtilities::getV1DataDir() . "/products/invoices/default_sample.jpg";
        $localInput = new PathInput($jpg);
        self::assertFalse($localInput->isPdf());
        $extractor = new PdfExtractor($localInput);
        self::assertSame(1, $extractor->pageCount);
    }

    /**
     *
     */
    public function testGivenAPdfShouldExtractInvoicesNoStrict(): void
    {
        $pdf = new PathInput(TestingUtilities::getV1DataDir() . "/products/invoice_splitter/invoice_5p.pdf");
        $response = $this->getPrediction();
        self::assertNotNull($response);
        $inference = $response->document->inference;
        $extractor = new PdfExtractor($pdf);
        self::assertSame(5, $extractor->pageCount);

        $extractedPdfSNoStrict = $extractor->extractInvoices($inference->prediction->invoicePageGroups);
        self::assertCount(3, $extractedPdfSNoStrict);
        self::assertSame("invoice_5p_001-001.pdf", $extractedPdfSNoStrict[0]->getFileName());
        self::assertSame(1, $extractedPdfSNoStrict[0]->pageCount);
        self::assertSame("invoice_5p_002-004.pdf", $extractedPdfSNoStrict[1]->getFileName());
        self::assertSame(3, $extractedPdfSNoStrict[1]->pageCount);
        self::assertSame("invoice_5p_005-005.pdf", $extractedPdfSNoStrict[2]->getFileName());
        self::assertSame(1, $extractedPdfSNoStrict[2]->pageCount);
    }

    /**
     *
     */
    public function testGivenAPdfShouldExtractInvoicesStrict(): void
    {
        $pdf = new PathInput(TestingUtilities::getV1DataDir() . "/products/invoice_splitter/invoice_5p.pdf");
        $response = $this->getPrediction();
        self::assertNotNull($response);
        $inference = $response->document->inference;

        $extractor = new PdfExtractor($pdf);
        self::assertSame(5, $extractor->pageCount);

        $extractedPdfStrict = $extractor->extractInvoices($inference->prediction->invoicePageGroups, true);
        self::assertCount(2, $extractedPdfStrict);
        self::assertSame("invoice_5p_001-001.pdf", $extractedPdfStrict[0]->getFileName());
        self::assertSame(1, $extractedPdfStrict[0]->pageCount);
        self::assertSame("invoice_5p_002-005.pdf", $extractedPdfStrict[1]->getFileName());
        self::assertSame(4, $extractedPdfStrict[1]->pageCount);
    }

    private function getPrediction()
    {
        $fileName = TestingUtilities::getV1DataDir() . "/products/invoice_splitter/response_v1/complete.json";
        $localResponse = new LocalResponse($fileName);
        return $this->dummyClient->loadPrediction(InvoiceSplitterV1::class, $localResponse);
    }
}
