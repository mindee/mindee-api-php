<?php

declare(strict_types=1);

namespace Input;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeePdfException;
use Mindee\Error\MindeeSourceException;
use Mindee\Image\ImageCompressor;
use Mindee\Input\Base64Input;
use Mindee\Input\BytesInput;
use Mindee\Input\FileInput;
use Mindee\Input\PageOptions;
use Mindee\Input\PathInput;
use Mindee\Pdf\PdfCompressor;
use Mindee\Pdf\PdfUtils;
use Mindee\V1\Client;
use PHPUnit\Framework\TestCase;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use TestingUtilities;

use function count;

use const Mindee\V1\Http\API_KEY_ENV_NAME;
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
            TestingUtilities::getRootDataDir() . "/output/compress_indirect.jpg",
            TestingUtilities::getRootDataDir() . "/output/compress100.jpg",
            TestingUtilities::getRootDataDir() . "/output/compress85.jpg",
            TestingUtilities::getRootDataDir() . "/output/compress50.jpg",
            TestingUtilities::getRootDataDir() . "/output/compress10.jpg",
            TestingUtilities::getRootDataDir() . "/output/compress1.jpg",
            TestingUtilities::getRootDataDir() . "/output/not_compressed.pdf",
            TestingUtilities::getRootDataDir() . "/output/compress_indirect.pdf",
            TestingUtilities::getRootDataDir() . "/output/not_compressed_multipage.pdf",
            TestingUtilities::getRootDataDir() . "/output/compress_direct_85.pdf",
            TestingUtilities::getRootDataDir() . "/output/compress_direct_75.pdf",
            TestingUtilities::getRootDataDir() . "/output/compress_direct_50.pdf",
            TestingUtilities::getRootDataDir() . "/output/compress_direct_10.pdf",
            TestingUtilities::getRootDataDir() . "/output/text_multipage.pdf",
        ];

        foreach ($filesToDelete as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }


    public function testPdfCountPages(): void
    {
        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        self::assertSame(12, $inputObj->getPageCount());
    }

    public function testPdfReconstructOK(): void
    {
        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([0, 1, 2, 3, 4], KEEP_ONLY, 2));
        self::assertSame(5, $inputObj->getPageCount());
    }

    public function testPdfReadContents(): void
    {
        $inputDoc = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $contents = $inputDoc->readContents();
        self::assertSame("multipage.pdf", $contents[0]);
    }

    /**
     * @dataProvider providePdfCutNPagesCases
     */
    public function testPdfCutNPages(array $indexes): void
    {
        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions($indexes, KEEP_ONLY, 2));
        try {
            $basePdf = new Fpdi();
            $cutPdf = new Fpdi();
            $pageCountCutPdf = $cutPdf->setSourceFile(
                TestingUtilities::getFileTypesDir() . "/pdf/multipage_cut-" . count($indexes) . ".pdf"
            );
            $pageCountBasePdf = $basePdf->setSourceFile($inputObj->fileObject->getFilename());
            $basePdf->Close();
            $cutPdf->Close();
            self::assertSame(count($indexes), $inputObj->getPageCount());
            self::assertSame($pageCountCutPdf, $pageCountBasePdf);

            $basePdf = new Fpdi();
            $cutPdf = new Fpdi();
            for ($pageNumber = 0; $pageNumber < $pageCountBasePdf; $pageNumber++) {
                $cutPdf->setSourceFile(TestingUtilities::getFileTypesDir() . "/pdf/multipage_cut-" . count($indexes) . ".pdf");
                $basePdf->setSourceFile($inputObj->fileObject->getFilename());
                $cutPdf->AddPage();
                $cutPdf->useTemplate($cutPdf->importPage($pageNumber + 1));
                $basePdf->AddPage();
                $basePdf->useTemplate($basePdf->importPage($pageNumber + 1));
            }
            $basePdf->Close();
            $cutPdf->Close();
        } catch (PdfParserException|PdfReaderException $e) {
            throw new MindeePdfException(
                "Failed to read PDF file.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }

    public static function providePdfCutNPagesCases(): iterable
    {
        return [[[0]], [[0, -2]], [[0, -2, -1]]];
    }

    public function testPdfKeep5FirstPages(): void
    {
        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([0, 1, 2, 3, 4], KEEP_ONLY, 2));
        self::assertSame(5, $inputObj->getPageCount());
    }

    public function testPdfKeepInvalidPages(): void
    {
        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([0, 1, 17], KEEP_ONLY, 2));
        self::assertSame(2, $inputObj->getPageCount());
    }

    public function testPdfRemove5LastPages(): void
    {

        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([-5, -4, -3, -2, -1], REMOVE, 2));
        self::assertSame(7, $inputObj->getPageCount());
    }

    public function testPdfRemove5FirstPages(): void
    {
        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([0, 1, 2, 3, 4], REMOVE, 2));
        self::assertSame(7, $inputObj->getPageCount());
    }

    public function testPdfRemoveInvalidPages(): void
    {
        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputObj->applyPageOptions(new PageOptions([16], REMOVE, 2));
        self::assertSame(12, $inputObj->getPageCount());
    }

    public function testPdfKeepNoPages(): void
    {
        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $this->expectException(MindeePdfException::class);
        $inputObj->applyPageOptions(new PageOptions([], KEEP_ONLY, 2));
    }

    public function testPdfRemoveAllPages(): void
    {
        $inputObj = new PathInput(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $this->expectException(MindeePdfException::class);
        $pageOptions = new PageOptions(range(0, $inputObj->getPageCount() - 1), REMOVE, 2);
        $inputObj->applyPageOptions(pageOptions: $pageOptions);
    }

    public function testPdfInputFromFile(): void
    {
        $fileContents = file_get_contents(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $fileRef = fopen(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf", "r");
        $inputDoc = new FileInput($fileRef);
        $contents = $inputDoc->readContents();
        self::assertSame("multipage.pdf", $contents[0]);
        self::assertSame($fileContents, $contents[1]);
    }

    public function testPdfInputFromBytes(): void
    {
        $pdfBytes = file_get_contents(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $inputDoc = new BytesInput($pdfBytes, "dummy.pdf");
        $contents = $inputDoc->readContents();
        self::assertSame("dummy.pdf", $contents[0]);
        self::assertSame($pdfBytes, $contents[1]);
    }

    public function testInputFromRawb64String(): void
    {
        $pdfBytes = file_get_contents(TestingUtilities::getFileTypesDir() . "/receipt.txt");
        $inputDoc = new Base64Input($pdfBytes, "dummy.pdf");
        $contents = $inputDoc->readContents();
        self::assertSame("dummy.pdf", $contents[0]);
        self::assertSame(str_replace("\n", "", $pdfBytes), str_replace("\n", "", base64_encode($contents[1])));
    }

    public function testShouldNotRaiseMimeErrorForBrokenFixablePdf(): void
    {
        $this->expectNotToPerformAssertions();

        $pathInput = new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/broken_fixable.pdf');
        $pathInput->fixPdf();
    }

    public function testShouldRaiseErrorForBrokenUnfixablePdf(): void
    {
        $this->expectException(MindeeSourceException::class);

        $pathInput = new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/broken_unfixable.pdf');
        $pathInput->fixPdf();
    }

    public function testShouldSendCorrectResultsForBrokenFixableInvoicePdf(): void
    {
        $sourceDocOriginal = new PathInput(
            TestingUtilities::getV1DataDir() . '/products/invoices/invoice.pdf'
        );

        $sourceDocFixed = new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/broken_invoice.pdf');
        $sourceDocFixed->fixPdf();
        self::assertSame($sourceDocFixed->readContents()[1], $sourceDocOriginal->readContents()[1]);
    }

    public function testImageQualityCompressionFromInputSource(): void
    {
        $receiptInput = new PathInput(TestingUtilities::getFileTypesDir() . '/receipt.jpg');
        $receiptInput->compress(80);
        file_put_contents(
            TestingUtilities::getRootDataDir() . "/output/compress_indirect.jpg",
            file_get_contents($receiptInput->fileObject->getFilename())
        );
        $sizeOriginal = filesize(TestingUtilities::getFileTypesDir() . '/receipt.jpg');
        $sizeCompressed = filesize(TestingUtilities::getRootDataDir() . "/output/compress_indirect.jpg");
        self::assertGreaterThan($sizeCompressed, $sizeOriginal);
    }

    public function testDirectImageQualityCompression(): void
    {
        $receiptInput = new PathInput(TestingUtilities::getFileTypesDir() . '/receipt.jpg');
        $sizeOriginal = filesize(TestingUtilities::getFileTypesDir() . '/receipt.jpg');
        $compresses = [
            100 => ImageCompressor::compress($receiptInput->fileObject, 100),
            85 => ImageCompressor::compress($receiptInput->fileObject),
            50 => ImageCompressor::compress($receiptInput->fileObject, 50),
            10 => ImageCompressor::compress($receiptInput->fileObject, 10),
            1 => ImageCompressor::compress($receiptInput->fileObject, 1),
        ];

        $outputFiles = [
            100 => TestingUtilities::getRootDataDir() . "/output/compress100.jpg",
            85 => TestingUtilities::getRootDataDir() . "/output/compress85.jpg",
            50 => TestingUtilities::getRootDataDir() . "/output/compress50.jpg",
            10 => TestingUtilities::getRootDataDir() . "/output/compress10.jpg",
            1 => TestingUtilities::getRootDataDir() . "/output/compress1.jpg",
        ];

        $compressSize = [];
        foreach ($compresses as $key => $value) {
            file_put_contents(
                $outputFiles[$key],
                file_get_contents($value->getFilename())
            );
            $compressSize[$key] = filesize($outputFiles[$key]);
        }
        self::assertGreaterThan($compressSize[85], $compressSize[100]);
        self::assertGreaterThan($sizeOriginal, $compressSize[85]);
        self::assertGreaterThan($compressSize[50], $sizeOriginal);
        self::assertGreaterThan($compressSize[10], $compressSize[50]);
        self::assertGreaterThan($compressSize[1], $compressSize[10]);
    }

    public function testPdfSourceText(): void
    {
        $imageInput = new PathInput(TestingUtilities::getFileTypesDir() . '/receipt.jpg');
        $pdfEmptyInput = new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/blank_1.pdf');
        $pdfSourceText = new PathInput(TestingUtilities::getFileTypesDir() . '/pdf/multipage.pdf');
        self::assertTrue($pdfSourceText->hasSourceText(), "Source text should be properly detected.");
        self::assertFalse($pdfEmptyInput->hasSourceText(), "Empty PDFs should not have source text detected.");
        self::assertFalse($imageInput->hasSourceText(), "An image should not have any text.");
    }

    public function testCompressPdfFromInputSource(): void
    {
        $pdfInput = new PathInput(
            TestingUtilities::getFileTypesDir() . "/pdf/not_blank_image_only.pdf"
        );
        self::assertFalse($pdfInput->hasSourceText());

        file_put_contents(
            TestingUtilities::getRootDataDir() . "/output/not_compressed.pdf",
            file_get_contents($pdfInput->fileObject->getFilename())
        );
        $sizeOriginal = filesize(TestingUtilities::getFileTypesDir() . '/pdf/not_blank_image_only.pdf');
        $sizeIgnored = filesize(TestingUtilities::getRootDataDir() . "/output/not_compressed.pdf");
        self::assertSame($sizeIgnored, $sizeOriginal);

        $pdfInput->compress(90, null, null, true, false);
        file_put_contents(
            TestingUtilities::getRootDataDir() . "/output/compress_indirect.pdf",
            file_get_contents($pdfInput->fileObject->getFilename())
        );
        $sizeCompressed = filesize(TestingUtilities::getRootDataDir() . '/output/compress_indirect.pdf');
        self::assertLessThan($sizeOriginal, $sizeCompressed);
    }

    public function testCompressPdfFromCompressor(): void
    {
        $pdfInput = new PathInput(
            TestingUtilities::getV1DataDir() . '/products/invoice_splitter/default_sample.pdf'
        );
        $sizeOriginal = filesize(TestingUtilities::getV1DataDir() . '/products/invoice_splitter/default_sample.pdf');

        self::assertFalse($pdfInput->hasSourceText());
        $pdfCompresses = [
            85 => PdfCompressor::compress($pdfInput->fileObject),
            75 => PdfCompressor::compress($pdfInput->fileObject, 75),
            50 => PdfCompressor::compress($pdfInput->fileObject, 50),
            10 => PdfCompressor::compress($pdfInput->fileObject, 10),
        ];
        $outputFiles = [
            85 => TestingUtilities::getRootDataDir() . "/output/compress_direct_85.pdf",
            75 => TestingUtilities::getRootDataDir() . "/output/compress_direct_75.pdf",
            50 => TestingUtilities::getRootDataDir() . "/output/compress_direct_50.pdf",
            10 => TestingUtilities::getRootDataDir() . "/output/compress_direct_10.pdf",
        ];

        $compressSize = [];
        foreach ($pdfCompresses as $key => $value) {
            file_put_contents(
                $outputFiles[$key],
                file_get_contents($value->getFilename())
            );
            $compressSize[$key] = filesize($outputFiles[$key]);
        }
        self::assertGreaterThan($compressSize[85], $sizeOriginal);
        self::assertGreaterThan($compressSize[75], $compressSize[85]);
        self::assertGreaterThan($compressSize[50], $compressSize[75]);
        self::assertGreaterThan($compressSize[10], $compressSize[50]);
    }

    public function testSourceTextPdfCompression(): void
    {

        $pdfInput = new PathInput(
            TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf"
        );

        self::assertTrue($pdfInput->hasSourceText());

        $pdfInput->compress(5, null, null, true, false);
        file_put_contents(
            TestingUtilities::getRootDataDir() . "/output/text_multipage.pdf",
            file_get_contents($pdfInput->fileObject->getFilename())
        );
        $sizeOriginal = filesize(TestingUtilities::getFileTypesDir() . "/pdf/multipage.pdf");
        $sizeTextCompressed = filesize(TestingUtilities::getRootDataDir() . "/output/text_multipage.pdf");
        self::assertSame($sizeTextCompressed, $sizeOriginal);

        self::assertSame(
            str_repeat('*', 650),
            implode('', str_replace(" ", "", PdfUtils::extractPagesTextElements(TestingUtilities::getRootDataDir() . "/output/text_multipage.pdf")))
        );
    }
}
