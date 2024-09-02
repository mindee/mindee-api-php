<?php

namespace Input;

use Mindee\Client;
use Mindee\Error\MindeePDFException;
use Mindee\Error\MindeeSourceException;
use Mindee\Input\PathInput;
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
    protected string $fileTypesDir;

    protected function setUp(): void
    {
        $this->oldKey = getenv(API_KEY_ENV_NAME);
        $this->dummyClient = new Client("dummy-key");
        putenv(API_KEY_ENV_NAME . '=');
        $this->fileTypesDir = (
            getenv('GITHUB_WORKSPACE') ?: "."
        ) . "/tests/resources/file_types/";
        $this->productsDir = (
            getenv('GITHUB_WORKSPACE') ?: "."
        ) . "/tests/resources/products/";
    }

    protected function tearDown(): void
    {
        putenv(API_KEY_ENV_NAME . '=' . $this->oldKey);
    }

    public function testPDFCountPages()
    {
        $inputObj = new PathInput($this->fileTypesDir . "pdf/multipage.pdf");
        $this->assertEquals(12, $inputObj->countDocPages());
    }

    public function testPDFReconstructOK()
    {
        $inputObj = new PathInput($this->fileTypesDir . "pdf/multipage.pdf");
        $inputObj->processPDF(KEEP_ONLY, 2, [0, 1, 2, 3, 4]);
        $this->assertEquals(5, $inputObj->countDocPages());
    }

    public function testPDFReadContents()
    {
        $inputDoc = new PathInput($this->fileTypesDir . "/pdf/multipage.pdf");
        $contents = $inputDoc->readContents();
        $this->assertEquals("multipage.pdf", $contents[0]);
    }

    /**
     * @dataProvider pageIndexesProvider
     */
    public function testPDFCutNPages(array $indexes)
    {
        $inputObj = new PathInput($this->fileTypesDir . "pdf/multipage.pdf");
        $inputObj->processPDF(KEEP_ONLY, 2, $indexes);
        try {
            $basePdf = new FPDI();
            $cutPdf = new FPDI();
            $pageCountCutPdf = $cutPdf->setSourceFile(
                $this->fileTypesDir . "pdf/multipage_cut-" . count($indexes) . ".pdf"
            );
            $pageCountBasePdf = $basePdf->setSourceFile($inputObj->fileObject->getFilename());
            $basePdf->Close();
            $cutPdf->Close();
            $this->assertEquals(count($indexes), $inputObj->countDocPages());
            $this->assertEquals($pageCountCutPdf, $pageCountBasePdf);

            $basePdf = new FPDI();
            $cutPdf = new FPDI();
            for ($pageNumber = 0; $pageNumber < $pageCountBasePdf; $pageNumber++) {
                $cutPdf->setSourceFile($this->fileTypesDir . "pdf/multipage_cut-" . count($indexes) . ".pdf");
                $basePdf->setSourceFile($inputObj->fileObject->getFilename());
                $cutPdf->AddPage();
                $cutPdf->useTemplate($cutPdf->importPage($pageNumber + 1));
                $basePdf->AddPage();
                $basePdf->useTemplate($basePdf->importPage($pageNumber + 1));
                // Note: comparing extracted page bytes content turns out to be unreliable when using FPDF.
                // This will be left here until a better solution is found within the limitations of licensing.
                //                $this->assertEquals($cutPdf->Output('', 'S'), $basePdf->Output('', 'S'));
            }
            $basePdf->Close();
            $cutPdf->Close();
        } catch (PdfParserException|PdfReaderException $e) {
            throw new MindeePDFException("Failed to read PDF file.");
        }
    }

    public function pageIndexesProvider()
    {
        return [[[0]], [[0, -2]], [[0, -2, -1]]];
    }

    public function testPDFKeep5FirstPages()
    {
        $inputObj = new PathInput($this->fileTypesDir . "pdf/multipage.pdf");
        $inputObj->processPDF(KEEP_ONLY, 2, [0, 1, 2, 3, 4]);
        $this->assertEquals(5, $inputObj->countDocPages());
    }

    public function testPDFKeepInvalidPages()
    {
        $inputObj = new PathInput($this->fileTypesDir . "pdf/multipage.pdf");
        $inputObj->processPDF(KEEP_ONLY, 2, [0, 1, 17]);
        $this->assertEquals(2, $inputObj->countDocPages());
    }

    public function testPDFRemove5LastPages()
    {

        $inputObj = new PathInput($this->fileTypesDir . "pdf/multipage.pdf");
        $inputObj->processPDF(REMOVE, 2, [-5, -4, -3, -2, -1]);
        $this->assertEquals(7, $inputObj->countDocPages());
    }

    public function testPDFRemove5FirstPages()
    {
        $inputObj = new PathInput($this->fileTypesDir . "pdf/multipage.pdf");
        $inputObj->processPDF(REMOVE, 2, [0, 1, 2, 3, 4]);
        $this->assertEquals(7, $inputObj->countDocPages());
    }

    public function testPDFRemoveInvalidPages()
    {
        $inputObj = new PathInput($this->fileTypesDir . "pdf/multipage.pdf");
        $inputObj->processPDF(REMOVE, 2, [16]);
        $this->assertEquals(12, $inputObj->countDocPages());
    }

    public function testPDFKeepNoPages()
    {
        $inputObj = new PathInput($this->fileTypesDir . "pdf/multipage.pdf");
        $this->expectException(MindeePDFException::class);
        $inputObj->processPDF(KEEP_ONLY, 2, []);
    }

    public function testPDFRemoveAllPages()
    {
        $inputObj = new PathInput($this->fileTypesDir . "pdf/multipage.pdf");
        $this->expectException(MindeePDFException::class);
        $inputObj->processPDF(REMOVE, 2, range(0, $inputObj->countDocPages() - 1));
    }

    public function testPDFInputFromFile()
    {
        $fileContents = file_get_contents($this->fileTypesDir . "/pdf/multipage.pdf");
        $fileRef = fopen($this->fileTypesDir . "/pdf/multipage.pdf", "r");
        $inputDoc = $this->dummyClient->sourceFromFile($fileRef);
        $contents = $inputDoc->readContents();
        $this->assertEquals("multipage.pdf", $contents[0]);
        $this->assertEquals($fileContents, $contents[1]);
    }

    public function testPDFInputFromBytes()
    {
        $pdfBytes = file_get_contents($this->fileTypesDir . "/pdf/multipage.pdf");
        $inputDoc = $this->dummyClient->sourceFromBytes($pdfBytes, "dummy.pdf");
        $contents = $inputDoc->readContents();
        $this->assertEquals("dummy.pdf", $contents[0]);
        $this->assertEquals($pdfBytes, $contents[1]);
    }

    public function testInputFromRawb64String()
    {
        $pdfBytes = file_get_contents($this->fileTypesDir . "/receipt.txt");
        $inputDoc = $this->dummyClient->sourceFromB64String($pdfBytes, "dummy.pdf");
        $contents = $inputDoc->readContents();
        $this->assertEquals("dummy.pdf", $contents[0]);
        $this->assertEquals(str_replace("\n", "", $pdfBytes), str_replace("\n", "", base64_encode($contents[1])));
    }


    public function testFileCloseValid()
    {
        $fileRef = fopen($this->fileTypesDir . "/pdf/multipage.pdf", "r");
        $inputDoc = $this->dummyClient->sourceFromFile($fileRef);
        $this->assertTrue(is_resource($inputDoc->getFilePtr()));
        $inputDoc->close();
        $this->assertFalse(is_resource($inputDoc->getFilePtr()));
    }

    public function testFileCloseInvalid()
    {
        $fileRef = fopen($this->fileTypesDir . "/pdf/multipage.pdf", "r");
        $inputDoc = $this->dummyClient->sourceFromFile($fileRef);
        $inputDoc->enableStrictMode();
        fclose($fileRef);
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage("File is already closed.");
        $inputDoc->close();
    }

    public function testFileCloseNotImplemented()
    {
        $pdfBytes = file_get_contents($this->fileTypesDir . "/receipt.txt");
        $inputDoc = $this->dummyClient->sourceFromb64String($pdfBytes, "dummy.pdf");
        $inputDoc->enableStrictMode();
        $this->expectException(MindeeSourceException::class);
        $this->expectExceptionMessage("Closing is not implemented on this type of local input source.");
        $inputDoc->close();
    }

    public function testShouldNotRaiseMimeErrorForBrokenFixablePdf()
    {
        $this->expectNotToPerformAssertions();

        $this->dummyClient->sourceFromPath($this->fileTypesDir . '/pdf/broken_fixable.pdf', true);
    }

    public function testShouldRaiseErrorForBrokenUnfixablePdf()
    {
        $this->expectException(MindeeSourceException::class);

        $this->dummyClient->sourceFromPath($this->fileTypesDir . '/pdf/broken_unfixable.pdf', true);
    }

    public function testShouldSendCorrectResultsForBrokenFixableInvoicePdf()
    {
        $sourceDocOriginal = $this->dummyClient->sourceFromPath($this->productsDir . '/invoices/invoice.pdf');

        $sourceDocFixed = $this->dummyClient->sourceFromPath($this->fileTypesDir . '/pdf/broken_invoice.pdf', true);
        $this->assertEquals($sourceDocFixed->readContents()[1], $sourceDocOriginal->readContents()[1]);
    }
}
