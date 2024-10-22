<?php

namespace Mindee\PDF;

use CURLFile;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeImageException;
use Mindee\Error\MindeePDFException;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Page;

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
        } catch (\Exception $e) {
            throw new MindeePDFException("Conversion to MagickImage failed.\n" . $e->getMessage());
        }
    }

    /**
     * Checks a PDF's stream content for text operators
     * See https://opensource.adobe.com/dc-acrobat-sdk-docs/pdfstandards/PDF32000_2008.pdf page 243-251.
     * @param string $data Stream data from a PDF's page.
     * @return boolean True if a text operator is found in the stream.
     */
    private static function streamHasText(string $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $textOperators = ['Tc', 'Tw', 'Th', 'TL', 'Tf', 'Tfs', 'Tk', 'Tr', 'Tm', 'T*', 'Tj', 'TJ', "'", '"'];
        foreach ($textOperators as $op) {
            if (strpos($data, $op) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks whether the file has source text. Returns false if the file isn't a PDF.
     * @param string $pdfPath Path to the PDF file.
     * @return boolean True if the PDF has source text, false otherwise.
     */
    public static function hasSourceText(string $pdfPath): bool
    {
        if (!file_exists($pdfPath) || !is_readable($pdfPath)) {
            return false;
        }

        $pdf = file_get_contents($pdfPath);
        if ($pdf === false) {
            return false;
        }

        if (substr($pdf, 0, 4) !== '%PDF') {
            return false;
        }

        preg_match_all('/stream\s(.*?)\sendstream/s', $pdf, $matches);

        foreach ($matches[1] as $stream) {
            if (self::streamHasText($stream)) {
                return true;
            }
        }

        return false;
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
                    $fontDetails = self::extractFontDetails($fonts, $text[1]);
                    $textElements[] = [
                        'text' => $text[0],
                        'x' => $text[2][0],
                        'y' => $text[2][1],
                        'font' => $fontDetails['name'],
                        'size' => $fontDetails['size'],
                        'color' => self::extractColor($text[1]),
                    ];
                }
            }

            return $textElements;
        } catch (\Exception $e) {
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
        $fontName = self::mapFontName($element['font']);
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
            return new CURLFile($path, 'application/pdf');
        } catch (\Exception $e) {
            throw new MindeeImageException(
                "Conversion to CURLFile failed.",
                ErrorCode::FILE_OPERATION_ABORTED,
                $e
            );
        }
    }
}
