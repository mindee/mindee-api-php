<?php

namespace V1\Input;

use Mindee\Client;
use Mindee\Product\Invoice\InvoiceV4;
use PHPUnit\Framework\TestCase;

class URLInputSourceTestFunctional extends TestCase
{
    protected Client $client;
    protected string $outputFilePath;
    protected string $referenceFilePath;

    protected function setUp(): void
    {
        $this->client = new Client();
        $this->outputFilePath = \TestingUtilities::getRootDataDir() . "/output/";
        $this->referenceFilePath = getenv('MINDEE_V2_SE_TESTS_BLANK_PDF_URL');
    }

    public static function tearDownAfterClass(): void
    {
        unlink(\TestingUtilities::getRootDataDir() . "/output/invoice_5p.pdf");
        unlink(\TestingUtilities::getRootDataDir() . "/output/customFileName.pdf");
    }

    public function testLoadLocalFile()
    {
        $urlSource = $this->client->sourceFromUrl($this->referenceFilePath);
        $localSource = $urlSource->asLocalInputSource();
        $result = $this->client->parse(InvoiceV4::class, $localSource);
        $this->assertEquals(5, $result->document->nPages);
        $this->assertEquals("invoice_5p.pdf", $result->document->filename);
    }

    public function testCustomFileName()
    {
        $urlSource = $this->client->sourceFromUrl($this->referenceFilePath);
        $localSource = $urlSource->asLocalInputSource("customName.pdf");
        $result = $this->client->parse(InvoiceV4::class, $localSource);
        $this->assertEquals(5, $result->document->nPages);
        $this->assertEquals("customName.pdf", $result->document->filename);
    }

    public function testSaveFile()
    {
        $urlSource = $this->client->sourceFromUrl($this->referenceFilePath);
        $urlSource->saveToFile($this->outputFilePath);
        $this->assertFileExists($this->outputFilePath . "invoice_5p.pdf");
    }

    public function testSaveFileWithFilename()
    {
        $urlSource = $this->client->sourceFromUrl($this->referenceFilePath);
        $urlSource->saveToFile($this->outputFilePath, "customFileName.pdf");
        $this->assertFileExists($this->outputFilePath . "customFileName.pdf");
    }
}
