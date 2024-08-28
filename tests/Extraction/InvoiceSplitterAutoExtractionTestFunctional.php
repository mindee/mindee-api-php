<?php

use Mindee\Client;
use Mindee\Extraction\PdfExtractor;
use Mindee\Input\PathInput;
use Mindee\Parsing\Common\Document;
use Mindee\Product\Invoice\InvoiceV4;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;
use PHPUnit\Framework\TestCase;
use Product\RegressionUtilities;

class PdfExtractorTest extends TestCase
{
    private const PRODUCT_DATA_DIR = 'resources/products';

    private function getInvoiceSplitter5pPath()
    {
        return self::PRODUCT_DATA_DIR . '/invoice_splitter/invoice_5p.pdf';
    }

    private function prepareInvoiceReturn(string $rstFilePath, Document $invoicePrediction): string
    {
        $rstContent = file_get_contents($rstFilePath);
        $parsingVersion = $invoicePrediction->inference->product->version;
        $parsingId = $invoicePrediction->id;
        $rstContent = str_replace(RegressionUtilities::getVersion($rstContent), $parsingVersion, $rstContent);
        $rstContent = str_replace(RegressionUtilities::getId($rstContent), $parsingId, $rstContent);
        return $rstContent;
    }

    /**
     * @test
     * @group regression
     */
    public function testPdfShouldExtractInvoicesStrict()
    {
        $client = new Client();
        $invoiceSplitterInput = new PathInput(self::PRODUCT_DATA_DIR . '/invoice_splitter/default_sample.pdf');
        $response = $client->enqueueAndParse(InvoiceSplitterV1::class, $invoiceSplitterInput);
        $inference = $response->document->inference;
        $pdfExtractor = new PdfExtractor($invoiceSplitterInput);
        $this->assertEquals(2, $pdfExtractor->getPageCount());

        $extractedPdfsStrict = $pdfExtractor->extractInvoices($inference->prediction->invoicePageGroups);

        $this->assertCount(2, $extractedPdfsStrict);
        $this->assertEquals('default_sample_001-001.pdf', $extractedPdfsStrict[0]->filename);
        $this->assertEquals('default_sample_002-002.pdf', $extractedPdfsStrict[1]->filename);

        $invoice0 = $client->parse(InvoiceV4::class, $extractedPdfsStrict[0]->asInputSource());
        $testStringRstInvoice0 = $this->prepareInvoiceReturn(
            self::PRODUCT_DATA_DIR . '/invoices/response_v4/summary_full_invoice_p1.rst',
            $invoice0->document
        );
        $this->assertEquals($testStringRstInvoice0, (string)$invoice0->document);
    }
}