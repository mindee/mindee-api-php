<?php

use Mindee\Client;
use Mindee\Extraction\PdfExtractor;
use Mindee\Input\PathInput;
use Mindee\Parsing\Common\Document;
use Mindee\Product\Invoice\InvoiceV4;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . "/../TestingUtilities.php");

class PdfExtractorTest extends TestCase
{
    private const PRODUCT_DATA_DIR = '/tests/resources/products';

    private function prepareInvoiceReturn(string $rstFilePath, Document $invoicePrediction): string
    {
        $fileContent = file_get_contents($rstFilePath);
        $parsingVersion = $invoicePrediction->inference->product->version;
        $parsingId = $invoicePrediction->id;
        $rstContent = str_replace(TestingUtilities::getVersion($fileContent), $parsingVersion, $fileContent);
        return str_replace(TestingUtilities::getId($rstContent), $parsingId, $rstContent);
    }

    /**
     * @test
     * @group functional
     */
    public function testPdfShouldExtractInvoicesStrict()
    {
        $client = new Client();
        $rootDir = (getenv('GITHUB_WORKSPACE') ?: ".") . self::PRODUCT_DATA_DIR;
        $invoiceSplitterInput = new PathInput(filePath: $rootDir . '/invoice_splitter/default_sample.pdf');
        $response = $client->enqueueAndParse(InvoiceSplitterV1::class, $invoiceSplitterInput);
        $inference = $response->document->inference;
        $pdfExtractor = new PdfExtractor($invoiceSplitterInput);
        $this->assertEquals(2, $pdfExtractor->getPageCount());

        $extractedPdfsStrict = $pdfExtractor->extractInvoices($inference->prediction->invoicePageGroups);

        $this->assertCount(2, $extractedPdfsStrict);
        $this->assertEquals('default_sample_001-001.pdf', $extractedPdfsStrict[0]->getFilename());
        $this->assertEquals('default_sample_002-002.pdf', $extractedPdfsStrict[1]->getFilename());

        $invoice0 = $client->parse(InvoiceV4::class, $extractedPdfsStrict[0]->asInputSource());

        $testStringRstInvoice0 = $this->prepareInvoiceReturn(
            rstFilePath: $rootDir . '/invoices/response_v4/summary_full_invoice_p1.rst',
            invoicePrediction: $invoice0->document
        );

        $this->assertGreaterThan(
            0.90,
            TestingUtilities::levenshteinRatio(
                $testStringRstInvoice0,
                (string)$invoice0->document
            )
        );
    }
}
