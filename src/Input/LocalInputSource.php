<?php

declare(strict_types=1);

/**
 * Local input handling.
 */

namespace Mindee\Input;

use BernardLedit\Pdf\ImageException;
use BernardLedit\Pdf\PdfDocument;
use BernardLedit\Pdf\PdfiumException;
use CURLFile;
use Exception;
use Mindee\Dependency\DependencyChecker;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeMimeTypeException;
use Mindee\Error\MindeePdfException;
use Mindee\Error\MindeeSourceException;
use Mindee\Error\MindeeUnhandledException;
use Mindee\Pdf\PdfUtils;
use Throwable;
use ValueError;

use function BernardLedit\Pdf\applyPageOptions as nativeApplyPageOptions;
use function BernardLedit\Pdf\fixPdf as nativeFixPdf;
use function in_array;
use function BernardLedit\Image\compress as compressImage;
use function BernardLedit\Pdf\compress as compressPdf;
use function strlen;

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
    'application/octet-stream',
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
    public ?string $filePath = null;

    /**
     * @var integer|null Page count.
     */
    public ?int $pageCount = null;

    /**
     * Base constructor, mostly used for Mime type checking.
     */
    public function __construct()
    {
        $this->checkMimeType();
        try {
            DependencyChecker::requireBernardLedit();
            if ($this->isPdf()) {
                $this->pageCount = $this->getPageCount();
            } else {
                $this->pageCount = 1;
            }
        } catch (MindeeUnhandledException) {
            error_log("PDF-handling features not available, page count set to null.");
        } catch (Throwable $e) {
            error_log("Could not open PDF due to exception '" . $e->getMessage() . "', setting page count to null.");
        }
    }

    /**
     * Checks if the file needs fixing.
     */
    public function checkNeedsFix(): void
    {
        if ($this->fileMimetype === 'application/octet-stream') {
            trigger_error(
                'File type application/octet-stream is probably incorrect. '
                . 'Try to run fixPDF() on the file.',
                E_USER_WARNING
            );
        }
    }

    /**
     * Checks the mimetype integrity of a file.
     *
     * @throws MindeeMimeTypeException Throws if the Mime type isn't allowed.
     */
    private function checkMimeType(): void
    {
        if (!in_array($this->fileMimetype, ALLOWED_MIME_TYPES, true)) {
            $fileTypes = implode(', ', ALLOWED_MIME_TYPES);
            throw new MindeeMimeTypeException(
                "File type "
                . $this->fileMimetype
                . " not allowed, must be one of $fileTypes.",
                ErrorCode::USER_OPERATION_ERROR
            );
        }
    }

    /**
     * Checks whether the file type is a PDF.
     *
     * @return boolean
     */
    public function isPdf(): bool
    {
        $this->checkMimeType();
        return $this->fileMimetype === 'application/pdf';
    }

    /**
     * Counts the amount of pages in a PDF.
     *
     * @return integer Page count.
     * @throws MindeePdfException Throws if the source pdf can't be properly processed.
     * @throws MindeeSourceException Throws if the source isn't a pdf.
     * @throws MindeeUnhandledException Throws if the bernard_ledit extension isn't loaded.
     */
    protected function getPageCount(): int
    {
        if (!$this->isPdf()) {
            throw new MindeeSourceException(
                "File is not a PDF.",
                ErrorCode::USER_OPERATION_ERROR
            );
        }
        DependencyChecker::requireBernardLedit();
        try {
            $pdf = new PdfDocument(file_get_contents($this->fileObject->getFilename()));
            return $pdf->getPageCount();
        } catch (PdfiumException $e) {
            throw new MindeePdfException(
                $e->getMessage(),
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        } catch (Exception $e) {
            throw new MindeePdfException(
                $e->getMessage(),
                ErrorCode::PDF_CANT_CREATE,
                $e
            );
        }
    }

    /**
     * @param string $fileBytes Raw data as bytes.
     */
    private function saveBytesAsFile(string $fileBytes): void
    {
        $cutPdfTempFile = tempnam(sys_get_temp_dir(), 'mindee_cut_pdf_');
        file_put_contents($cutPdfTempFile, $fileBytes);
        $this->filePath = $cutPdfTempFile;
        $this->fileObject = new CURLFile($cutPdfTempFile, $this->fileMimetype, $this->fileName);
    }

    /**
     * Checks whether the contents of a PDF are empty.
     *
     * @return boolean
     * @throws MindeePdfException Throws if the pdf file can't be processed.
     * @throws MindeeUnhandledException Throws if the bernard_ledit extension isn't loaded.
     */
    public function isPdfEmpty(): bool
    {
        DependencyChecker::requireBernardLedit();
        try {
            $pdf = new PdfDocument(file_get_contents($this->fileObject->getFilename()));
            try {
                return $pdf->hasNoContent();
            } finally {
                $pdf->close();
            }
        } catch (Exception $e) {
            throw new MindeePdfException($e->getMessage(), ErrorCode::PDF_CANT_CREATE, $e);
        }
    }


    /**
     * Reads the contents of the file.
     * @return array{0: string, 1: string} File name and contents as a tuple.
     */
    public function readContents(): array
    {
        $fileHandle = fopen($this->fileObject->getFilename(), 'r');
        $strContents = fread($fileHandle, filesize($this->fileObject->getFilename()));
        fclose($fileHandle);
        return [basename($this->fileObject->getFilename()), $strContents];
    }

    /**
     * Attempts to fix a PDF file.
     *
     * @throws MindeeSourceException Throws if the file couldn't be fixed.
     */
    public function fixPdf(int $maximumOffset = 500): void
    {
        if (str_starts_with($this->fileMimetype, "image/")) {
            error_log("Input file is an image, skipping PDF fix.");
            return;
        }
        DependencyChecker::requireBernardLedit();
        try {
            $fixed = nativeFixPdf(file_get_contents($this->fileObject->getFilename()), $maximumOffset);
        } catch (ValueError $e) {
            throw new MindeeSourceException($e->getMessage(), ErrorCode::FILE_OPERATION_ERROR, $e);
        }
        $this->fileMimetype = 'application/pdf';
        $this->saveBytesAsFile($fixed);
    }

    /**
     * Compress a file and update the file object (does not alter the original file).
     *
     * @param integer $quality Quality of the output file.
     * @param integer|null $maxWidth Maximum width (Ignored for PDFs).
     * @param integer|null $maxHeight Maximum height (Ignored for PDFs).
     * @param boolean $optimize Whether to optimize the output file.
     *                          Disabling this may result in a larger file, which will revert to the original.
     * @param float|null $dpi DPI of the output file.
     */
    public function compress(
        int    $quality = 85,
        ?int   $maxWidth = null,
        ?int   $maxHeight = null,
        ?bool  $optimize = true,
        ?float $dpi = 72,
    ): void {
        $bytes = file_get_contents($this->fileObject->getFilename());
        if ($this->isPdf()) {
            $compressed = compressPdf($bytes, $quality, $optimize, $dpi);
        } else {
            [$compressed] = compressImage($bytes, $quality, $maxWidth, $maxHeight, $optimize);
        }
        $this->saveBytesAsFile($compressed);
    }

    /**
     * Checks the source file for source text.
     *
     * @return boolean Returns false if none is found, or if the file isn't a PDF.
     * @throws Exception Throws if an instance of pdf-parser can't be created.
     */
    public function hasSourceText(): bool
    {
        if (!$this->isPdf()) {
            return false;
        }
        return PdfUtils::hasSourceText(file_get_contents($this->fileObject->getFilename()));
    }


    /**
     * Applies PDF-specific operations on the current file based on the specified PageOptions.
     *
     * @param PageOptions|null $pageOptions The options specifying which pages to modify or retain in the PDF file.
     * @throws MindeePdfException If a PDF processing error occurs during the operation.
     */
    public function applyPageOptions(?PageOptions $pageOptions): void
    {
        if ($pageOptions === null) {
            return;
        }
        if (!$this->isPdf()) {
            throw new MindeeSourceException(
                "File is not a PDF: " . $this->fileName,
                ErrorCode::USER_OPERATION_ERROR
            );
        }
        DependencyChecker::requireBernardLedit();
        try {
            [$document, $skipped] = nativeApplyPageOptions(
                file_get_contents($this->fileObject->getFilename()),
                $pageOptions->operation,
                $pageOptions->onMinPage,
                array_values($pageOptions->pageIndexes)
            );
        } catch (PdfiumException $e) {
            throw new MindeePdfException($e->getMessage(), ErrorCode::PDF_CANT_PROCESS, $e);
        } catch (ValueError $e) {
            throw new MindeePdfException($e->getMessage(), ErrorCode::USER_OPERATION_ERROR, $e);
        }
        foreach ($skipped as $pageId) {
            error_log("Page index '" . $pageId . "' is not present in source document");
        }
        if ($document === null) {
            return;
        }
        try {
            $this->saveBytesAsFile($document->asBytes());
            $this->pageCount = $document->getPageCount();
        } finally {
            $document->close();
        }
    }

    /**
     * Returns the file size of the current file.
     * @return integer The file size in bytes.
     */
    public function getFileSize(): int
    {
        $bytes = file_get_contents($this->fileObject->getFilename());
        return strlen($bytes);
    }
}
