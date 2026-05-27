<?php

declare(strict_types=1);

namespace Mindee\Pdf;

use CURLFile;
use Exception;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeImageException;
use Mindee\Error\MindeePdfException;
use Smalot\PdfParser\Config;
use Smalot\PdfParser\Font;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Parser;
use Imagick;
use SplFileObject;
use TypeError;

use function is_resource;
use function is_string;

/**
 * PDF utility class.
 */
class PdfUtils
{
    /**
     * @param SplFileObject|Imagick|CURLFile|string|resource $input Input file. Accepts SplFileObject, Imagick, Curl, resources & paths.
     * @return string Path of the file.
     * @throws MindeePdfException Throws if a path can't be extracted from the input.
     */
    public static function extractFilePath(mixed $input): string
    {
        if (is_string($input) && file_exists($input) && is_file($input)) {
            return $input;
        }
        try {
            if ($input instanceof Imagick) {
                return $input->getImageFilename();
            } elseif ($input instanceof SplFileObject) {
                return $input->getRealPath();
            } elseif ($input instanceof CURLFile) {
                return $input->getFilename();
            } elseif (is_resource($input)) {
                $metaData = stream_get_meta_data($input);
                if (isset($metaData['uri']) && is_file($metaData['uri'])) {
                    return $metaData['uri'];
                }
                rewind($input);
                $tempPath = tempnam(sys_get_temp_dir(), 'mindee_ext_') . '.pdf';
                file_put_contents($tempPath, stream_get_contents($input));

                return $tempPath;
            } else {
                throw new MindeePdfException('Input PDF must be a SplFileObject, path, resource or Imagick handle.');
            }
        } catch (MindeePdfException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new MindeePdfException(
                "Conversion to MagickImage failed.\n",
                ErrorCode::IMAGE_CANT_PROCESS,
                $e
            );
        }
    }

    /**
     * Checks whether the file has source text. Returns false if the file isn't a PDF.
     * @param string $pdfPath Path to the PDF file.
     * @return boolean True if the PDF has source text, false otherwise.
     * @throws Exception Throws if an instance of pdf-parser can't be created.
     */
    public static function hasSourceText(string $pdfPath): bool
    {
        $config = new Config();
        $config->setDataTmFontInfoHasToBeIncluded(true);
        $parser = new Parser([], $config);
        $pdf = $parser->parseFile($pdfPath);
        return $pdf->getText() !== '';
    }

    /**
     * Extracts text elements with their properties from all pages in a PDF.
     *
     * @param string $pdfPath Path to the PDF file.
     * @return array<integer, string> A page-indexed array of text elements.
     *                                Each text element includes text content, position, font, size, and color.
     * @throws MindeePdfException Throws if the PDF can't be parsed or text elements can't be extracted.
     */
    public static function extractPagesTextElements(string $pdfPath): array
    {
        try {
            $config = new Config();
            $config->setDataTmFontInfoHasToBeIncluded(true);
            $parser = new Parser([], $config);
            $pdf = $parser->parseFile($pdfPath);
            $allPagesTextElements = [];

            foreach ($pdf->getPages() as $pageNumber => $page) {
                $result = self::extractTextElements($page);
                $text = implode('', array_map(static fn($e) => $e['text'], $result));
                $allPagesTextElements[$pageNumber] = $text;
            }

            return $allPagesTextElements;
        } catch (Exception $e) {
            throw new MindeePdfException(
                'Failed to parse PDF or extract text elements: ',
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }


    /**
     * Downgrades PDF files unsupported by FPDI to a compatible version.
     *
     * @param string $inputPath Input PDF path.
     * @return string Output path.
     * @throws MindeePdfException Throws if the file can't be handled through ghostscript.
     * @throws Exception Will be thrown as MindeePdfException, this is just for PHPCS linting purposes.
     */
    public static function downgradePdfVersion(string $inputPath): string
    {
        try {
            $outputPath = tempnam(sys_get_temp_dir(), 'downgrade_pdf_') . '.pdf';
            $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/prepress -dNOPAUSE -dQUIET"
                . " -dBATCH -sOutputFile=$outputPath \"$inputPath\"";

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                unlink($outputPath);
                throw new Exception();
            }

            return $outputPath;
        } catch (Exception $e) {
            throw new MindeePdfException(
                "Cannot downgrade PDF version.",
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }


    /**
     * Extracts text elements with their properties from a PDF page.
     *
     * @param Page $page Page object.
     * @return array<array<string, string|float|Font|null>> An array of text elements, each containing text content,
     *                                                      position, font, size, and color.
     * @throws MindeePdfException Throws if the text elements can't be extracted.
     */
    public static function extractTextElements(Page $page): array
    {
        try {
            $dataTm = $page->getDataTm();
        } catch (Exception|TypeError) {
            return [];
        }
        try {
            $textElements = [];
            foreach ($dataTm as $text) {
                if (isset($text[1])) {
                    $textElements[] = [
                        'text' => $text[1],
                        'rotation' => rad2deg((float) ($text[0][2])),
                        'x' => (float) ($text[0][4]),
                        'y' => (float) ($text[0][5]),
                        'font' => $page->getFont($text[2]),
                        'size' => (float) ($text[3]),
                    ];
                }
            }

            return $textElements;
        } catch (Exception $e) {
            throw new MindeePdfException(
                'Failed to parse text elements: ',
                ErrorCode::PDF_CANT_PROCESS,
                $e
            );
        }
    }

    /**
     * @param string $fontName Name of the font/subfont.
     * @return array{family: string, style: string} The standard font & possible style.
     */
    protected static function standardizeFontName(string $fontName): array
    {
        $cleanName = preg_replace('/^.*?\+/', '', $fontName);
        $parts = explode('-', (string) $cleanName, 2);

        $fontFamily = $parts[0];
        $fontStyle = $parts[1] ?? '';

        if ($fontStyle === $fontFamily) {
            $fontStyle = '';
        }
        $fontStyle = str_replace(['Bold', 'Italic', 'Oblique'], ['B', 'I', 'I'], $fontStyle);
        if (str_contains($fontStyle, 'B') && str_contains($fontStyle, 'I')) {
            $fontStyle = 'BI';
        }

        return [
            'family' => $fontFamily,
            'style' => $fontStyle,
        ];
    }

    /**
     * Adds a text element to the output PDF.
     *
     * @param CustomFpdi $pdf The output PDF object.
     * @param array<string, float|null|Font|string> $element Text element array containing text, position, font, size, and color.
     */
    public static function addTextElement(CustomFpdi $pdf, array $element): void
    {
        $fontInfo = static::standardizeFontName($element['font']->getName());
        $pageHeight = $pdf->GetPageHeight();

        $size = $element['size'] * 3;
        $x = $element['x'] - $size / 10;
        $y = $pageHeight - $element['y'] - $size / 10;
        $pdf->SetFont($fontInfo['family'], $fontInfo['style'], $size);

        $pdf->SetTextColor(0, 0, 0); // No currently reliable nor easy way of retrieving text color.

        $pdf->SetXY($x, $y);
        $pdf->startTransform();
        $pdf->rotate($element['rotation'], $x, $y);
        $pdf->Cell(0, 0, $element['text']);
        $pdf->stopTransform();
    }

    /**
     * Loads a pdf handle into a valid CURLFile handle.
     * @param string $path Imagick image handle.
     * @throws MindeeImageException Throws if the image can't be converted back into a CURLFile.
     */
    public static function toCURLFile(string $path): CURLFile
    {
        try {
            $postFileName = pathinfo($path, PATHINFO_FILENAME);
            return new CURLFile($path, 'application/pdf', $postFileName . ".pdf");
        } catch (Exception $e) {
            throw new MindeeImageException(
                "Conversion to CURLFile failed.",
                ErrorCode::FILE_OPERATION_ABORTED,
                $e
            );
        }
    }
}
