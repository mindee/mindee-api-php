<?php

namespace Input;

use Mindee\Client;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeePDFException;
use Mindee\Error\MindeeSourceException;
use Mindee\Image\ImageCompressor;
use Mindee\Input\PageOptions;
use Mindee\Input\PathInput;
use Mindee\Input\FileInput;
use Mindee\Input\BytesInput;
use Mindee\Input\Base64Input;
use Mindee\PDF\PDFCompressor;
use Mindee\PDF\PDFUtils;
use PHPUnit\Framework\TestCase;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfReader\PdfReaderException;

use const Mindee\Http\API_KEY_ENV_NAME;
use const Mindee\Input\KEEP_ONLY;
use const Mindee\Input\REMOVE;

class LocalInputSourceTest extends TestCase
{
    private string $oldKey;
    protected Client $dummyClient;

    protected function setUp(): void
    {
        $this->oldKey = getenv(API_KEY_ENV_NAME);
        $this->dummyClient = new Client("dummy-key");
        putenv(API_KEY_ENV_NAME . '=');
    }

    protected function tearDown(): void
    {
        putenv(API_KEY_ENV_NAME . '=' . $this->oldKey);

        $filesToDelete = [
            \TestingUtilities::getRootDataDir() . "/output/compress_indirect.jpg",
            \TestingUtilities::getRootDataDir() . "/output/compress100.jpg",
            \TestingUtilities::getRootDataDir() . "/output/compress85.jpg",
            \TestingUtilities::getRootDataDir() . "/output/compress50.jpg",
            \TestingUtilities::getRootDataDir() . "/output/compress10.jpg",
            \TestingUtilities::getRootDataDir() . "/output/compress1.jpg",
            \TestingUtilities::getRootDataDir() . "/output/not_compressed.pdf",
            \TestingUtilities::getRootDataDir() . "/output/compress_indirect.pdf",
            \TestingUtilities::getRootDataDir() . "/output/not_compressed_multipage.pdf",
            \TestingUtilities::getRootDataDir() . "/output/compress_direct_85.pdf",
            \TestingUtilities::getRootDataDir() . "/output/compress_direct_75.pdf",
            \TestingUtilities::getRootDataDir() . "/output/compress_direct_50.pdf",
            \TestingUtilities::getRootDataDir() . "/output/compress_direct_10.pdf",
            \TestingUtilities::getRootDataDir() . "/output/text_multipage.pdf"
        ];

        foreach ($filesToDelete as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }


    public function testPDFCountPages()
    {
        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $this->assertEquals(12, $inputObj->getPageCount());
    }

    public function testPDFReconstructOK()
    {
        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([0, 1, 2, 3, 4], KEEP_ONLY, 2));
        $this->assertEquals(5, $inputObj->getPageCount());
    }

    public function testPDFReadContents()
    {
        $inputDoc = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $contents = $inputDoc->readContents();
        $this->assertEquals("multipage.pdf", $contents[0]);
    }

    /**
     * @dataProvider pageIndexesProvider
     */
    public function testPDFCutNPages(array $indexes)
    {
        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions($indexes, KEEP_ONLY, 2));
        try {
            $basePdf = new FPDI();
            $cutPdf = new FPDI();
            $pageCountCutPdf = $cutPdf->setSourceFile(
                \TestingUtilities::getFileTypesDir() . "/pdf/multipage_cut-" . count($indexes) . ".pdf"
            );
            $pageCountBasePdf = $basePdf->setSourceFile($inputObj->fileObject->getFilename());
            $basePdf->Close();
            $cutPdf->Close();
            $this->assertEquals(count($indexes), $inputObj->getPageCount());
            $this->assertEquals($pageCountCutPdf, $pageCountBasePdf);

            $basePdf = new FPDI();
            $cutPdf = new FPDI();
            for ($pageNumber = 0; $pageNumber < $pageCountBasePdf; $pageNumber++) {
                $cutPdf->setSourceFile(\TestingUtilities::getFileTypesDir() . "/pdf/multipage_cut-" . count($indexes) . ".pdf");
                $basePdf->setSourceFile($inputObj->fileObject->getFilename());
                $cutPdf->AddPage();
                $cutPdf->useTemplate($cutPdf->importPage($pageNumber + 1));
                $basePdf->AddPage();
                $basePdf->useTemplate($basePdf->importPage($pageNumber + 1));
                // TODO: comparing extracted page bytes content turns out to be unreliable when using FPDF.
                // This will be left here until a better solution is found within the limitations of licensing.
                //                $this->assertEquals($cutPdf->Output('', 'S'), $basePdf->Output('', 'S'));
            }
            $basePdf->Close();
            $cutPdf->Close();
        } catch (PdfParserException | PdfReaderException $e) {
            throw new MindeePDFException(
                "Failed to read PDF file.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }

    public function pageIndexesProvider()
    {
        return [[[0]], [[0, -2]], [[0, -2, -1]]];
    }

    public function testPDFKeep5FirstPages()
    {
        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([0, 1, 2, 3, 4], KEEP_ONLY, 2));
        $this->assertEquals(5, $inputObj->getPageCount());
    }

    public function testPDFKeepInvalidPages()
    {
        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([0, 1, 17], KEEP_ONLY, 2));
        $this->assertEquals(2, $inputObj->getPageCount());
    }

    public function testPDFRemove5LastPages()
    {

        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([-5, -4, -3, -2, -1], REMOVE, 2));
        $this->assertEquals(7, $inputObj->getPageCount());
    }

    public function testPDFRemove5FirstPages()
    {
        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([0, 1, 2, 3, 4], REMOVE, 2));
        $this->assertEquals(7, $inputObj->getPageCount());
    }

    public function testPDFRemoveInvalidPages()
    {
        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([16], REMOVE, 2));
        $this->assertEquals(12, $inputObj->getPageCount());
    }

    public function testPDFKeepNoPages()
    {
        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $this->expectException(MindeePDFException::class);
        $inputObj->applyPageOptions(new PageOptions([], KEEP_ONLY, 2));
    }

    public function testPDFRemoveAllPages()
    {
        $inputObj = new PathInput(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $this->expectException(MindeePDFException::class);
        $pageOptions = new PageOptions(range(0, $inputObj->getPageCount() - 1), REMOVE, 2);
        $inputObj->applyPageOptions(pageOptions: $pageOptions);
    }

    public function testPDFInputFromFile()
    {
        $fileContents = file_get_contents(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $fileRef = fopen(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf", "r");
        $inputDoc = new FileInput($fileRef);
        $contents = $inputDoc->readContents();
        $this->assertEquals("multipage.pdf", $contents[0]);
        $this->assertEquals($fileContents, $contents[1]);
    }

    public function testPDFInputFromBytes()
    {
        $pdfBytes = file_get_contents(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputDoc = new BytesInput($pdfBytes, "dummy.pdf");
        $contents = $inputDoc->readContents();
        $this->assertEquals("dummy.pdf", $contents[0]);
        $this->assertEquals($pdfBytes, $contents[1]);
    }

    public function testInputFromRawb64String()
    {
        $pdfBytes = file_get_contents(\TestingUtilities::getFileTypesDir() . "/receipt.txt");
        $inputDoc = new Base64Input($pdfBytes, "dummy.pdf");
        $contents = $inputDoc->readContents();
        $this->assertEquals("dummy.pdf", $contents[0]);
        $this->assertEquals(str_replace("\n", "", $pdfBytes), str_replace("\n", "", base64_encode($contents[1])));
    }

    public function testShouldNotRaiseMimeErrorForBrokenFixablePdf()
    {
        $this->expectNotToPerformAssertions();

        $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . '/pdf/broken_fixable.pdf', true);
    }

    public function testShouldRaiseErrorForBrokenUnfixablePdf()
    {
        $this->expectException(MindeeSourceException::class);

        $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . '/pdf/broken_unfixable.pdf', true);
    }

    public function testShouldSendCorrectResultsForBrokenFixableInvoicePdf()
    {
        $sourceDocOriginal = $this->dummyClient->sourceFromPath(
            \TestingUtilities::getV1DataDir() . '/products/invoices/invoice.pdf'
        );

        $sourceDocFixed = $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . '/pdf/broken_invoice.pdf', true);
        $this->assertEquals($sourceDocFixed->readContents()[1], $sourceDocOriginal->readContents()[1]);
    }

    public function testImageQualityCompressionFromInputSource()
    {
        $receiptInput = $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . '/receipt.jpg');
        $receiptInput->compress(80);
        file_put_contents(
            \TestingUtilities::getRootDataDir() . "/output/compress_indirect.jpg",
            file_get_contents($receiptInput->fileObject->getFilename())
        );
        $sizeOriginal = filesize(\TestingUtilities::getFileTypesDir() . '/receipt.jpg');
        $sizeCompressed = filesize(\TestingUtilities::getRootDataDir() . "/output/compress_indirect.jpg");
        $this->assertGreaterThan($sizeCompressed, $sizeOriginal);
    }

    public function testDirectImageQualityCompression()
    {
        $receiptInput = $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . '/receipt.jpg');
        $sizeOriginal = filesize(\TestingUtilities::getFileTypesDir() . '/receipt.jpg');
        $compresses = [
            100 => ImageCompressor::compress($receiptInput->fileObject, 100),
            85 => ImageCompressor::compress($receiptInput->fileObject),
            50 => ImageCompressor::compress($receiptInput->fileObject, 50),
            10 => ImageCompressor::compress($receiptInput->fileObject, 10),
            1 => ImageCompressor::compress($receiptInput->fileObject, 1)
        ];

        $outputFiles = [
            100 => \TestingUtilities::getRootDataDir() . "/output/compress100.jpg",
            85 => \TestingUtilities::getRootDataDir() . "/output/compress85.jpg",
            50 => \TestingUtilities::getRootDataDir() . "/output/compress50.jpg",
            10 => \TestingUtilities::getRootDataDir() . "/output/compress10.jpg",
            1 => \TestingUtilities::getRootDataDir() . "/output/compress1.jpg",
        ];

        $compressSize = [];
        foreach ($compresses as $key => $value) {
            file_put_contents(
                $outputFiles[$key],
                file_get_contents($value->getFilename())
            );
            $compressSize[$key] = filesize($outputFiles[$key]);
        }
        $this->assertGreaterThan($compressSize[85], $compressSize[100]);
        $this->assertGreaterThan($sizeOriginal, $compressSize[85]);
        $this->assertGreaterThan($compressSize[50], $sizeOriginal);
        $this->assertGreaterThan($compressSize[10], $compressSize[50]);
        $this->assertGreaterThan($compressSize[1], $compressSize[10]);
    }

    public function testPDFSourceText()
    {
        $imageInput = $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . '/receipt.jpg');
        $pdfEmptyInput = $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf');
        $pdfSourceText = $this->dummyClient->sourceFromPath(\TestingUtilities::getFileTypesDir() . '/pdf/multipage.pdf');
        $this->assertTrue($pdfSourceText->hasSourceText(), "Source text should be properly detected.");
        $this->assertFalse($pdfEmptyInput->hasSourceText(), "Empty PDFs should not have source text detected.");
        $this->assertFalse($imageInput->hasSourceText(), "An image should not have any text.");
    }

    public function testCompressPDFFromInputSource()
    {
        $pdfInput = $this->dummyClient->sourceFromPath(
            \TestingUtilities::getFileTypesDir() . "/pdf/not_blank_image_only.pdf"
        );
        $this->assertFalse($pdfInput->hasSourceText());

        file_put_contents(
            \TestingUtilities::getRootDataDir() . "/output/not_compressed.pdf",
            file_get_contents($pdfInput->fileObject->getFilename())
        );
        $sizeOriginal = filesize(\TestingUtilities::getFileTypesDir() . '/pdf/not_blank_image_only.pdf');
        $sizeIgnored = filesize(\TestingUtilities::getRootDataDir() . "/output/not_compressed.pdf");
        $this->assertEquals($sizeIgnored, $sizeOriginal);

        $pdfInput->compress(90, null, null, true, false);
        file_put_contents(
            \TestingUtilities::getRootDataDir() . "/output/compress_indirect.pdf",
            file_get_contents($pdfInput->fileObject->getFilename())
        );
        $sizeCompressed = filesize(\TestingUtilities::getRootDataDir() . '/output/compress_indirect.pdf');
        $this->assertLessThan($sizeOriginal, $sizeCompressed);
    }

    public function testCompressPDFFromCompressor()
    {
        $pdfInput = $this->dummyClient->sourceFromPath(
            \TestingUtilities::getV1DataDir() . '/products/invoice_splitter/default_sample.pdf'
        );
        $sizeOriginal = filesize(\TestingUtilities::getV1DataDir() . '/products/invoice_splitter/default_sample.pdf');

        $this->assertFalse($pdfInput->hasSourceText());
        $pdfCompresses = [
            85 => PDFCompressor::compress($pdfInput->fileObject),
            75 => PDFCompressor::compress($pdfInput->fileObject, 75),
            50 => PDFCompressor::compress($pdfInput->fileObject, 50),
            10 => PDFCompressor::compress($pdfInput->fileObject, 10),
        ];
        $outputFiles = [
            85 => \TestingUtilities::getRootDataDir() . "/output/compress_direct_85.pdf",
            75 => \TestingUtilities::getRootDataDir() . "/output/compress_direct_75.pdf",
            50 => \TestingUtilities::getRootDataDir() . "/output/compress_direct_50.pdf",
            10 => \TestingUtilities::getRootDataDir() . "/output/compress_direct_10.pdf",
        ];

        $compressSize = [];
        foreach ($pdfCompresses as $key => $value) {
            file_put_contents(
                $outputFiles[$key],
                file_get_contents($value->getFilename())
            );
            $compressSize[$key] = filesize($outputFiles[$key]);
        }
        $this->assertGreaterThan($compressSize[85], $sizeOriginal);
        $this->assertGreaterThan($compressSize[75], $compressSize[85]);
        $this->assertGreaterThan($compressSize[50], $compressSize[75]);
        $this->assertGreaterThan($compressSize[10], $compressSize[50]);
    }

    public function testSourceTextPDFCompression()
    {

        $pdfInput = $this->dummyClient->sourceFromPath(
            \TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf"
        );

        $this->assertTrue($pdfInput->hasSourceText());

        $pdfInput->compress(5, null, null, true, false);
        file_put_contents(
            \TestingUtilities::getRootDataDir() . "/output/text_multipage.pdf",
            file_get_contents($pdfInput->fileObject->getFilename())
        );
        $sizeOriginal = filesize(\TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $sizeTextCompressed = filesize(\TestingUtilities::getRootDataDir() . "/output/text_multipage.pdf");
        $this->assertEquals($sizeTextCompressed, $sizeOriginal);
        // Note: Greater size when compressed is expected due to original not having any images, so the operation will
        // be aborted.

        $this->assertEquals(
            str_repeat('*', 650),
            implode('', str_replace(" ", "", PDFUtils::extractPagesTextElements(\TestingUtilities::getRootDataDir() . "/output/text_multipage.pdf")))
        );
    }
}
