<?php

/**
 * Local input handling.
 */

namespace Mindee\Input;

use CURLFile;
use Exception;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeImageException;
use Mindee\Error\MindeeMimeTypeException;
use Mindee\Error\MindeePDFException;
use Mindee\Error\MindeeSourceException;
use Mindee\Error\MindeeUnhandledException;
use Mindee\Image\ImageCompressor;
use Mindee\Parsing\DependencyChecker;
use Mindee\PDF\PDFCompressor;
use Mindee\PDF\PDFUtils;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfReader\PdfReaderException;

/**
 * List of allowed mime types for document parsing.
 */
const ALLOWED_MIME_TYPES = [
    'application/pdf',
    'image/heic',
    'image/png',
    'image/jpg',
    'image/jpeg',
    'image/tiff',
    'image/webp',
];

/**
 * Base class for all input sources coming from the local machine.
 */
abstract class LocalInputSource extends InputSource
{
    /**
     * @var CURLFile File object, as a CURLFile for simplicity.
     */
    public CURLFile $fileObject;
    /**
     * @var string Name of the file, mandatory for proper Mime type handling server-side.
     */
    public string $fileName;
    /**
     * @var string File Mime type, as a string.
     */
    public string $fileMimetype;
    /**
     * @var string|null Path of the file for files retrieved from a path.
     */
    public ?string $filePath;

    /**
     * @var boolean Sets the input mode to debug. Only used in unit tests.
     */
    protected bool $throwsOnClose;

    /**
     * Checks the mimetype integrity of a file.
     *
     * @return void
     * @throws MindeeMimeTypeException Throws if the Mime type isn't allowed.
     */
    private function checkMimeType()
    {
        if (!in_array($this->fileMimetype, ALLOWED_MIME_TYPES)) {
            $fileTypes = implode(', ', ALLOWED_MIME_TYPES);
            throw new MindeeMimeTypeException(
                "File type " .
                $this->fileMimetype .
                " not allowed, must be one of $fileTypes.",
                ErrorCode::USER_OPERATION_ERROR
            );
        }
    }

    /**
     * Base constructor, mostly used for Mime type checking.
     * @param boolean $fixPDF Whether the PDF should be fixed or not.
     */
    public function __construct(bool $fixPDF = false)
    {
        if ($fixPDF) {
            $this->fixPDF();
        }
        $this->checkMimeType();
        $this->throwsOnClose = false;
    }

    /**
     * Checks whether the file type is a PDF.
     *
     * @return boolean
     */
    public function isPDF(): bool
    {
        return $this->fileMimetype == 'application/pdf';
    }

    /**
     * Counts the amount of pages in a PDF.
     *
     * @return integer
     * @throws MindeePDFException Throws if the source pdf can't be properly processed.
     * @throws MindeeSourceException Throws if the source isn't a pdf.
     */
    public function countDocPages(): int
    {
        if (!$this->isPDF()) {
            throw new MindeeSourceException(
                "File is not a PDF.",
                ErrorCode::USER_OPERATION_ERROR
            );
        }
        $pdf = new FPDI();
        try {
            return $pdf->setSourceFile($this->fileObject->getFilename());
        } catch (PdfParserException $e) {
            throw new MindeePDFException(
                "Failed to read PDF file.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }

    /**
     * Processes a PDF file.
     * To be implemented.
     *
     * @param string  $behavior    Behaviors available: KEEP_ONLY, REMOVE.
     * @param integer $onMinPages  Minimum of pages to apply the operation.
     * @param array   $pageIndexes Indexes of the pages to apply the operation to.
     * @return void
     * @throws MindeePDFException Throws if the operation is unknown, or if the resulting PDF can't be processed.
     */
    public function processPDF(string $behavior, int $onMinPages, array $pageIndexes)
    {
        if ($this->isPDFEmpty()) {
            throw new MindeePDFException(
                "Pages are empty in PDF file.",
                ErrorCode::USER_INPUT_ERROR
            );
        }
        if ($this->countDocPages() < $onMinPages) {
            return;
        }
        $allPages = range(0, $this->countDocPages() - 1);
        $pagesToKeep = [];
        if ($behavior == KEEP_ONLY) {
            foreach ($pageIndexes as $pageId) {
                if ($pageId < 0) {
                    $pageId = $this->countDocPages() + $pageId;
                }
                if (!in_array($pageId, $allPages)) {
                    error_log("Page index '" . $pageId . "' is not present in source document");
                } else {
                    $pagesToKeep[] = $pageId;
                }
            }
        } elseif ($behavior == REMOVE) {
            $pagesToRemove = [];
            foreach ($pageIndexes as $pageId) {
                if ($pageId < 0) {
                    $pageId = $this->countDocPages() + $pageId;
                }
                if (!in_array($pageId, $allPages)) {
                    error_log("Page index '" . $pageId . "' is not present in source document");
                } else {
                    $pagesToRemove[] = $pageId;
                }
            }
            $pagesToKeep = array_diff($allPages, $pagesToRemove);
        } else {
            throw new MindeePDFException(
                "Unknown operation '" . $behavior . "'.",
                ErrorCode::USER_OPERATION_ERROR
            );
        }
        if (count($pagesToKeep) < 1) {
            throw new MindeePDFException(
                "Resulting PDF would have no pages left.",
                ErrorCode::USER_OPERATION_ERROR
            );
        }
        $this->mergePDFPages($pagesToKeep);
    }

    /**
     * @param string $fileBytes Raw data as bytes.
     * @return void
     */
    private function saveBytesAsFile(string $fileBytes)
    {
        $cutPdfTempFile = tempnam(sys_get_temp_dir(), 'mindee_cut_pdf_');
        file_put_contents($cutPdfTempFile, $fileBytes);
        $this->filePath = $cutPdfTempFile;
        $this->fileObject = new CURLFile($cutPdfTempFile, $this->fileMimetype, $this->fileName);
    }

    /**
     * Create a new PDF from pages and set it as the main file object.
     * @param array $pageNumbers Array of page numbers to add to the newly created PDF.
     * @return void
     * @throws MindeePDFException Throws if the pdf file can't be processed.
     */
    public function mergePDFPages(array $pageNumbers)
    {
        try {
            $pdf = new FPDI();
            $pdf->setSourceFile($this->filePath);
            foreach ($pageNumbers as $pageNumber) {
                $pdf->AddPage();
                $pdf->useTemplate($pdf->importPage($pageNumber + 1));
            }
            $this->saveBytesAsFile($pdf->Output($this->fileName, 'S'));
            $pdf->Close();
        } catch (PdfParserException | PdfReaderException $e) {
            throw new MindeePDFException(
                "Failed to read PDF file.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }

    /**
     * Checks whether the contents of a PDF are empty.
     * @param integer $threshold Semi-arbitrary threshold of minimum bytes on the page for it to be considered empty.
     *
     * @return boolean
     * @throws MindeePDFException Throws if the pdf file can't be processed.
     */
    public function isPDFEmpty(int $threshold = 1024): bool
    {
        try {
            $pdf = new FPDI();
            $pageCount = $pdf->setSourceFile($this->fileObject->getFilename());
            $pdf->Close();
            for ($pageNumber = 0; $pageNumber < $pageCount; $pageNumber++) {
                $pdfPage = new FPDI();
                $pdfPage->setSourceFile($this->fileObject->getFilename());
                $pdfPage->AddPage();
                $pdfPage->useTemplate($pdfPage->importPage($pageNumber + 1));
                if (strlen($pdfPage->Output('', 'S')) > $threshold) {
                    $pdfPage->Close();
                    return false;
                }
                $pdfPage->Close();
            }
        } catch (PdfParserException | PdfReaderException $e) {
            throw new MindeePDFException(
                "Failed to read PDF file.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
        return true;
    }

    /**
     * Reads the contents of the file.
     *
     * @return array
     */
    public function readContents(): array
    {
        $fileHandle = fopen($this->fileObject->getFilename(), 'rb');
        $strContents = fread($fileHandle, filesize($this->fileObject->getFilename()));
        fclose($fileHandle);
        return [basename($this->fileObject->getFilename()), $strContents];
    }


    /**
     * Attempts to fix a PDF file.
     *
     * @return void
     * @throws MindeeSourceException Throws if the file couldn't be fixed.
     */
    private function fixPDF(): void
    {
        if (str_starts_with($this->fileMimetype, "image/")) {
            error_log("Input file is an image, skipping PDF fix.");
            return;
        }
        $bytesContent = file_get_contents($this->fileObject->getFilename());

        $pdfMarkerPosition = strrpos(strtoupper($bytesContent), '%PDF');

        if ($pdfMarkerPosition !== false) {
            $tempFile = tempnam(sys_get_temp_dir(), 'pdf_fix_');
            rename($tempFile, $tempFile .= "." . pathinfo($this->fileName, PATHINFO_EXTENSION));
            file_put_contents($tempFile, substr($bytesContent, $pdfMarkerPosition));

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $this->fileMimetype = finfo_file($finfo, $tempFile);
            finfo_close($finfo);
            $this->fileObject = new CURLFile($tempFile, $this->fileMimetype, $this->fileName);
            return;
        }

        throw new MindeeSourceException(
            "PDF file could not be fixed.",
            ErrorCode::FILE_OPERATION_ERROR
        );
    }

    /**
     * Closes the handle/stream, if the input type supports it.
     *
     * @return void
     * @throws MindeeSourceException Throws when strict mode is enabled.
     */
    public function close(): void
    {
        if ($this->throwsOnClose) {
            throw new MindeeSourceException(
                "Closing is not implemented on this type of local input source.",
                ErrorCode::USER_OPERATION_ERROR
            );
        } else {
            error_log("Closing is not implemented on this type of local input source.");
        }
    }

    /**
     * Enables strict mode.
     * Currently only used to throw on misuse of close().
     *
     * @return void
     */
    public function enableStrictMode()
    {
        $this->throwsOnClose = true;
    }

    /**
     * @param integer      $quality                    Quality of the output file.
     * @param integer|null $maxWidth                   Maximum width (Ignored for PDFs).
     * @param integer|null $maxHeight                  Maximum height (Ignored for PDFs).
     * @param boolean      $forceSourceTextCompression Whether to force the operation on PDFs with source text.
     *            This will attempt to re-render PDF text over the rasterized original.
     *            The script will attempt to re-write text, but might not support all fonts & encoding.
     *            If disabled, ignored the operation.
     *            WARNING: this operation is strongly discouraged.
     * @param boolean      $disableSourceText          If the PDF has source text, whether to re-apply it to the
     *            original or not. Needs force_source_text to work.
     * @return void
     */
    public function compress(
        int $quality = 85,
        int $maxWidth = null,
        int $maxHeight = null,
        bool $forceSourceTextCompression = false,
        bool $disableSourceText = true
    ): void {
        if ($this->isPDF()) {
            $this->fileObject = PDFCompressor::compress(
                $this->fileObject,
                $quality,
                $forceSourceTextCompression,
                $disableSourceText
            );
            $this->fileMimetype = 'application/pdf';
            $pathInfo = pathinfo($this->filePath);
            $this->filePath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.pdf';
        } else {
            $this->fileObject = ImageCompressor::compress(
                $this->fileObject,
                $quality,
                $maxWidth,
                $maxHeight
            );
            $this->fileMimetype = 'image/jpeg';
            $pathInfo = pathinfo($this->filePath);
            $this->filePath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.jpg';
        }
    }

    /**
     * Checks the source file for source text.
     *
     * @return boolean Returns false if none is found, or if the file isn't a PDF.
     * @throws Exception Throws if an instance of pdf-parser can't be created.
     */
    public function hasSourceText(): bool
    {
        if (!$this->isPDF()) {
            return false;
        }
        return PDFUtils::hasSourceText($this->filePath);
    }


    /**
     * Applies PDF-specific operations on the current file based on the specified PageOptions.
     *
     * @param PageOptions|null $pageOptions The options specifying which pages to modify or retain in the PDF file.
     * @return void
     * @throws MindeePDFException If a PDF processing error occurs during the operation.
     */
    public function applyPageOptions(?PageOptions $pageOptions): void
    {
        if ($pageOptions !== null && $this->isPDF()) {
            $this->processPDF(
                $pageOptions->operation,
                $pageOptions->onMinPage,
                $pageOptions->pageIndexes
            );
        }
    }
}
