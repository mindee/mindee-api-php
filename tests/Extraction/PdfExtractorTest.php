<?php


namespace Extraction;

use Mindee\Client;
use Mindee\Extraction\PdfExtractor;
use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;
use PHPUnit\Framework\TestCase;

class PdfExtractorTest extends TestCase
{

    private Client $dummyClient;

    protected function setUp(): void
    {
        $this->dummyClient = new Client("dummy-key");
    }
    public function testGivenAnImageShouldExtractAPDF()
    {
        $jpg = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/invoices/default_sample.jpg";
        $localInput = new PathInput($jpg);
        $this->assertFalse($localInput->isPdf());
        $extractor = new PdfExtractor($localInput);
        $this->assertEquals(1, $extractor->getPageCount());
    }

    /**
     * @test
     */
    public function testGivenAPDFShouldExtractInvoicesNoStrict()
    {
        $pdf = new PathInput((getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/invoice_splitter/invoice_5p.pdf");
        $response = $this->getPrediction();
        $this->assertNotNull($response);
        $inference = $response->document->inference;
        $extractor = new PdfExtractor($pdf);
        $this->assertEquals(5, $extractor->getPageCount());

        $extractedPDFSNoStrict = $extractor->extractInvoices($inference->prediction->invoicePageGroups);
        $this->assertCount(3, $extractedPDFSNoStrict);
        $this->assertEquals("invoice_5p_001-001.pdf", $extractedPDFSNoStrict[0]->getFileName());
        $this->assertEquals(1, $extractedPDFSNoStrict[0]->getPageCount());
        $this->assertEquals("invoice_5p_002-004.pdf", $extractedPDFSNoStrict[1]->getFileName());
        $this->assertEquals(3, $extractedPDFSNoStrict[1]->getPageCount());
        $this->assertEquals("invoice_5p_005-005.pdf", $extractedPDFSNoStrict[2]->getFileName());
        $this->assertEquals(1, $extractedPDFSNoStrict[2]->getPageCount());
    }

    /**
     * @test
     */
    public function testGivenAPDFShouldExtractInvoicesStrict()
    {
        $pdf = new PathInput((getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/invoice_splitter/invoice_5p.pdf");
        $response = $this->getPrediction();
        $this->assertNotNull($response);
        $inference = $response->document->inference;

        $extractor = new PdfExtractor($pdf);
        $this->assertEquals(5, $extractor->getPageCount());

        $extractedPDFStrict = $extractor->extractInvoices($inference->prediction->invoicePageGroups, true);
        $this->assertCount(2, $extractedPDFStrict);
        $this->assertEquals("invoice_5p_001-001.pdf", $extractedPDFStrict[0]->getFileName());
        $this->assertEquals(1, $extractedPDFStrict[0]->getPageCount());
        $this->assertEquals("invoice_5p_002-005.pdf", $extractedPDFStrict[1]->getFileName());
        $this->assertEquals(4, $extractedPDFStrict[1]->getPageCount());
    }

    private function getPrediction()
    {
        $fileName = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/invoice_splitter/response_v1/complete.json";
        $localResponse = new LocalResponse($fileName);
        return $this->dummyClient->loadPrediction(InvoiceSplitterV1::class, $localResponse);
    }
}
