<?php

declare(strict_types=1);

namespace V1\Input;

use Mindee\V1\Client;
use Mindee\V1\Product\Invoice\InvoiceV4;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class UrlInputSourceTestFunctional extends TestCase
{
    protected Client $client;
    protected string $outputFilePath;
    protected string $referenceFilePath;

    protected function setUp(): void
    {
        $this->client = new Client();
        $this->outputFilePath = TestingUtilities::getRootDataDir() . "/output/";
        $this->referenceFilePath = getenv('MINDEE_V2_SE_TESTS_BLANK_PDF_URL');
    }

    public static function tearDownAfterClass(): void
    {
        unlink(TestingUtilities::getRootDataDir() . "/output/blank_1.pdf");
        unlink(TestingUtilities::getRootDataDir() . "/output/customFileName.pdf");
    }

    public function testLoadLocalFile(): void
    {
        $urlSource = new UrlInputSource($this->referenceFilePath);
        $localSource = $urlSource->asLocalInputSource();
        $result = $this->client->parse(InvoiceV4::class, $localSource);
        self::assertSame(1, $result->document->nPages);
        self::assertSame("blank_1.pdf", $result->document->filename);
    }

    public function testCustomFileName(): void
    {
        $urlSource = new UrlInputSource($this->referenceFilePath);
        $localSource = $urlSource->asLocalInputSource("customName.pdf");
        $result = $this->client->parse(InvoiceV4::class, $localSource);
        self::assertSame(1, $result->document->nPages);
        self::assertSame("customName.pdf", $result->document->filename);
    }

    public function testSaveFile(): void
    {
        $urlSource = new UrlInputSource($this->referenceFilePath);
        $urlSource->saveToFile($this->outputFilePath);
        self::assertFileExists($this->outputFilePath . "blank_1.pdf");
    }

    public function testSaveFileWithFilename(): void
    {
        $urlSource = new UrlInputSource($this->referenceFilePath);
        $urlSource->saveToFile($this->outputFilePath, "customFileName.pdf");
        self::assertFileExists($this->outputFilePath . "customFileName.pdf");
    }
}
