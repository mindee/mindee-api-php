<?php

declare(strict_types=1);

/**
 * Local input handling.
 */

namespace Mindee\Input;

use CURLFile;
use Exception;
use Mindee\Dependency\DependencyChecker;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeMimeTypeException;
use Mindee\Error\MindeePdfException;
use Mindee\Error\MindeeSourceException;
use Mindee\Error\MindeeUnhandledException;
use Mindee\Image\ImageCompressor;
use Mindee\Pdf\PdfCompressor;
use Mindee\Pdf\PdfUtils;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfReader\PdfReaderException;

use function count;
use function in_array;
use function strlen;

use const DIRECTORY_SEPARATOR;

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
     * Base constructor, mostly used for Mime type checking.
     */
    public function __construct()
    {
        $this->checkMimeType();
        try {
            DependencyChecker::isGhostscriptAvailable();
            if ($this->isPdf()) {
                $this->pageCount = $this->getPageCount();
            } else {
                $this->pageCount = 1;
            }
        } catch (MindeeUnhandledException) {
            error_log("PDF-handling features not available, page count set to null.");
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
     * @return integer
     * @throws MindeePdfException Throws if the source pdf can't be properly processed.
     * @throws MindeeSourceException Throws if the source isn't a pdf.
     */
    protected function getPageCount(): int
    {
        if (!$this->isPdf()) {
            throw new MindeeSourceException(
                "File is not a PDF.",
                ErrorCode::USER_OPERATION_ERROR
            );
        }
        $pdf = new Fpdi();
        try {
            return $pdf->setSourceFile($this->fileObject->getFilename());
        } catch (PdfParserException $e) {
            throw new MindeePdfException(
                "Failed to read PDF file.",
                ErrorCode::PDF_CANT_PROCESS,
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
     * Create a new PDF from pages and set it as the main file object.
     * @param array<integer> $pageNumbers Array of page numbers to add to the newly created PDF.
     * @throws MindeePdfException Throws if the pdf file can't be processed.
     */
    public function mergePdfPages(array $pageNumbers): void
    {
        try {
            $pdf = new Fpdi();
            $pdf->setSourceFile($this->filePath);
            foreach ($pageNumbers as $pageNumber) {
                $pdf->AddPage();
                $pdf->useTemplate($pdf->importPage($pageNumber + 1));
            }
            $this->saveBytesAsFile($pdf->Output($this->fileName, 'S'));
            $pdf->Close();
        } catch (PdfParserException|PdfReaderException $e) {
            throw new MindeePdfException(
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
     * @throws MindeePdfException Throws if the pdf file can't be processed.
     */
    public function isPdfEmpty(int $threshold = 1024): bool
    {
        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($this->fileObject->getFilename());
            $pdf->Close();
            for ($pageNumber = 0; $pageNumber < $pageCount; $pageNumber++) {
                $pdfPage = new Fpdi();
                $pdfPage->setSourceFile($this->fileObject->getFilename());
                $pdfPage->AddPage();
                $pdfPage->useTemplate($pdfPage->importPage($pageNumber + 1));
                if (strlen((string) $pdfPage->Output('', 'S')) > $threshold) {
                    $pdfPage->Close();
                    return false;
                }
                $pdfPage->Close();
            }
        } catch (PdfParserException|PdfReaderException $e) {
            throw new MindeePdfException(
                "Failed to read PDF file.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
        return true;
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
    public function fixPdf(): void
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
     * @param integer $quality Quality of the output file.
     * @param integer|null $maxWidth Maximum width (Ignored for PDFs).
     * @param integer|null $maxHeight Maximum height (Ignored for PDFs).
     * @param boolean $forceSourceTextCompression Whether to force the operation on PDFs with source text.
     *                                            This will attempt to re-render PDF text over the rasterized original.
     *                                            The script will attempt to re-write text, but might not support all fonts & encoding.
     *                                            If disabled, ignored the operation.
     *                                            WARNING: this operation is strongly discouraged.
     * @param boolean $disableSourceText If the PDF has source text, whether to re-apply it to the
     *                                   original or not. Needs force_source_text to work.
     */
    public function compress(
        int $quality = 85,
        ?int $maxWidth = null,
        ?int $maxHeight = null,
        bool $forceSourceTextCompression = false,
        bool $disableSourceText = true
    ): void {
        if ($this->isPdf()) {
            $this->fileObject = PdfCompressor::compress(
                $this->fileObject,
                $quality,
                $forceSourceTextCompression,
                $disableSourceText
            );
            $this->fileMimetype = 'application/pdf';
            $pathInfo = pathinfo((string) $this->filePath);
            $this->filePath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.pdf';
        } else {
            $this->fileObject = ImageCompressor::compress(
                $this->fileObject,
                $quality,
                $maxWidth,
                $maxHeight
            );
            $this->fileMimetype = 'image/jpeg';
            $pathInfo = pathinfo((string) $this->filePath);
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
        if (!$this->isPdf()) {
            return false;
        }
        return PdfUtils::hasSourceText($this->filePath);
    }


    /**
     * Applies PDF-specific operations on the current file based on the specified PageOptions.
     *
     * @param PageOptions|null $pageOptions The options specifying which pages to modify or retain in the PDF file.
     * @throws MindeePdfException If a PDF processing error occurs during the operation.
     */
    public function applyPageOptions(?PageOptions $pageOptions): void
    {
        if ($this->isPdfEmpty()) {
            throw new MindeePdfException(
                "Pages are empty in PDF file.",
                ErrorCode::USER_INPUT_ERROR
            );
        }
        if ($this->getPageCount() < $pageOptions->onMinPage) {
            return;
        }
        $allPages = range(0, $this->getPageCount() - 1);
        $pagesToKeep = [];
        if ($pageOptions->operation === KEEP_ONLY) {
            foreach ($pageOptions->pageIndexes as $pageId) {
                if ($pageId < 0) {
                    $pageId = $this->getPageCount() + $pageId;
                }
                if (!in_array($pageId, $allPages, true)) {
                    error_log("Page index '" . $pageId . "' is not present in source document");
                } else {
                    $pagesToKeep[] = $pageId;
                }
            }
        } elseif ($pageOptions->operation === REMOVE) {
            $pagesToRemove = [];
            foreach ($pageOptions->pageIndexes as $pageId) {
                if ($pageId < 0) {
                    $pageId = $this->getPageCount() + $pageId;
                }
                if (!in_array($pageId, $allPages, true)) {
                    error_log("Page index '" . $pageId . "' is not present in source document");
                } else {
                    $pagesToRemove[] = $pageId;
                }
            }
            $pagesToKeep = array_diff($allPages, $pagesToRemove);
        } else {
            throw new MindeePdfException(
                "Unknown operation '" . $pageOptions->operation . "'.",
                ErrorCode::USER_OPERATION_ERROR
            );
        }
        if (count($pagesToKeep) < 1) {
            throw new MindeePdfException(
                "Resulting PDF would have no pages left.",
                ErrorCode::USER_OPERATION_ERROR
            );
        }
        $this->mergePdfPages($pagesToKeep);
        $this->pageCount = $this->getPageCount();
    }
}
