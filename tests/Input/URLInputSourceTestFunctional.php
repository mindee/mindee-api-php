<?php

namespace Input;

use Mindee\Client;
use Mindee\Product\Invoice\InvoiceV4;
use PHPUnit\Framework\TestCase;

class URLInputSourceTestFunctional extends TestCase
{
    protected Client $client;
    protected string $fileTypesDir;
    protected string $outputFilePath;
    protected string $referenceFilePath;

    protected function setUp(): void
    {
        $this->client = new Client();
        $this->outputFilePath = (getenv('GITHUB_WORKSPACE') ?: ".") .
            "/tests/resources/output/";
        $this->referenceFilePath = "https://github.com/mindee/client-lib-test-data/blob/main/products/invoice_splitter/invoice_5p.pdf?raw=true";
    }

    public static function tearDownAfterClass(): void
    {
        unlink((getenv('GITHUB_WORKSPACE') ?: ".") .
            "/tests/resources/output/invoice_5p.pdf");
        unlink((getenv('GITHUB_WORKSPACE') ?: ".") .
            "/tests/resources/output/customFileName.pdf");
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
