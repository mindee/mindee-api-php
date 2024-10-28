<?php

namespace Mindee\PDF;

use CURLFile;
use Exception;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeImageException;
use Mindee\Error\MindeePDFException;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Parser;

/**
 * PDF utility class.
 */
class PDFUtils
{
    /**
     * @param mixed $input Input file. Accepts SplFileObject, Imagick, Curl, resources & paths.
     * @return string Path of the file.
     * @throws MindeePDFException Throws if a path can't be extracted from the input.
     */
    public static function extractFilePath($input): string
    {
        if (is_string($input) && file_exists($input) && is_file($input)) {
            return $input;
        }
        try {
            if ($input instanceof \Imagick) {
                return $input->getImageFilename();
            } elseif ($input instanceof \SplFileObject) {
                return $input->getRealPath();
            } elseif ($input instanceof CURLFile) {
                return $input->getFilename();
            } elseif (is_resource($input)) {
                $imagickHandle = new \Imagick();
                $imagickHandle->readImageBlob($input);
            } else {
                throw new MindeePDFException('Input PDF must be a SplFileObject, path, resource or Imagick handle.');
            }
            $imagickHandle->setImageFormat('jpeg');
            return $imagickHandle;
        } catch (MindeePDFException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new MindeePDFException("Conversion to MagickImage failed.\n" . $e->getMessage());
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
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        return strlen($pdf->getText()) > 0;
    }

    /**
     * Extracts text elements with their properties from a PDF page.
     *
     * @param Page $page Page object.
     * @return array An array of text elements, each containing text content, position, font, size, and color.
     * @throws MindeePDFException Throws if the text elements can't be extracted.
     */
    public static function extractTextElements(Page $page): array
    {
        try {
            $textElements = [];
            $fonts = $page->getFonts();

            foreach ($page->getTextArray() as $text) {
                if (isset($text[1])) {
                    $fontDetails = static::extractFontDetails($fonts, $text[1]);
                    $textElements[] = [
                        'text' => $text[0],
                        'x' => $text[2][0],
                        'y' => $text[2][1],
                        'font' => $fontDetails['name'],
                        'size' => $fontDetails['size'],
                        'color' => static::extractColor($text[1]),
                    ];
                }
            }

            return $textElements;
        } catch (Exception $e) {
            throw new MindeePDFException('Failed to parse text elements: ' . $e->getMessage());
        }
    }

    /**
     * Extracts font details from the font array of a page.
     *
     * @param array $fonts   Array of Font objects from the PDF page.
     * @param array $details Font details array from a text element.
     * @return array Associative array with 'name' and 'size' of the font
     */
    private static function extractFontDetails(array $fonts, array $details): array
    {
        $fontName = 'Arial';
        $fontSize = 12;

        foreach ($fonts as $font) {
            if ($font->getDetails() === $details) {
                $fontName = $font->getName();
                $fontSize = $font->getDetails()['FontSize'] ?? 12;
                break;
            }
        }

        return ['name' => $fontName, 'size' => $fontSize];
    }

    /**
     * Extracts the color from text. Defaults to black if color information is not available.
     *
     * @param array $details Text bit.
     * @return int[] RGB values.
     */
    private static function extractColor(array $details): array
    {
        return $details['Color'] ?? [0, 0, 0];
    }

    /**
     * Adds a text element to the output PDF.
     *
     * @param FPDI  $pdf     The output PDF object.
     * @param array $element Text element array containing text, position, font, size, and color.
     * @return void
     */
    public static function addTextElement(FPDI $pdf, array $element): void
    {
        $fontName = static::mapFontName($element['font']);
        $pdf->SetFont($fontName, '', $element['size']);

        $pdf->SetTextColor($element['color'][0], $element['color'][1], $element['color'][2]);

        $pdf->SetXY($element['x'], $element['y']);
        $pdf->Write(0, $element['text']);
    }

    /**
     * @param string $originalFont Original font name.
     * @return string Corresponding standard font. Defaults to Helvetica.
     */
    private static function mapFontName(string $originalFont): string
    {
        $fontMap = [
            'Helvetica' => 'Helvetica',
            'Times' => 'Times',
            'Courier' => 'Courier',
            'Arial' => 'Arial'
        ];

        return $fontMap[$originalFont] ?? 'Helvetica';
    }

    /**
     * Loads a pdf handle into a valid CURLFile handle.
     * @param string $path Imagick image handle.
     * @return CURLFile
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
