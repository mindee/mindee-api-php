<?php
// bernard-ledit v0.1.0

// Stubs for bernard_ledit

namespace BernardLedit\Image {
    class Image {
        /**
         * Decodes an image from its encoded bytes: `new Image(string $data)`.
         *
         * @param string $data
         * @throws \BernardLedit\Image\ImageException if `$data` isn't an identifiable image, or can't be decoded.
         */
        public function __construct(string $data) {}

        /**
         * Crops the image to the box from (`$left`, `$top`) to (`$right`, `$bottom`), in pixels; the
         * right and bottom edges are excluded.
         *
         * @param int<0, 4294967295> $left
         * @param int<0, 4294967295> $top
         * @param int<0, 4294967295> $right
         * @param int<0, 4294967295> $bottom
         * @return \BernardLedit\Image\Image
         * @throws \ValueError if a coordinate is out of range, or the box is empty or extends past the image.
         */
        public function crop(int $left, int $top, int $right, int $bottom): \BernardLedit\Image\Image {}

        /**
         * Crops the bounding box of a polygon from this image.
         *
         * @param list<array{0: float, 1: float}> $polygon Normalized points relative to the image size.
         * @return \BernardLedit\Image\Image
         * @throws \ValueError if the polygon is empty, malformed, or its bounding box is empty.
         * @throws \BernardLedit\Image\ImageException if cropping fails.
         */
        public function cropPolygon(array $polygon): \BernardLedit\Image\Image {}

        /**
         * Returns the encoded bytes as a PHP string.
         *
         * @param string $format An output format: `"JPEG"`, `"PNG"`, `"PDF"`, etc.
         * @param int<1, 100> $jpeg_quality Only used by JPEG and PDF.
         * @param bool $jpeg_optimize Optimize JPEG Huffman tables.
         * @return string
         * @throws \ValueError if `$format` isn't a supported output format, or `$jpeg_quality` is out of range.
         * @throws \BernardLedit\Image\ImageException if encoding fails.
         */
        public function encode(string $format, int $jpeg_quality = 85, bool $jpeg_optimize = true): string {}

        /**
         * Upper-case format name (`"JPEG"`, `"PNG"`, etc.) or `null`.
         *
         * @return string|null
         */
        public function getFormat(): ?string {}

        /**
         * Height.
         *
         * @return int
         */
        public function getHeight(): int {}

        /**
         * Dimensions in pixels.
         *
         * @return array{0: int, 1: int} `[$width, $height]`.
         */
        public function getSize(): array {}

        /**
         * Width.
         *
         * @return int
         */
        public function getWidth(): int {}

        /**
         * Resizes the image to `$width` by `$height` pixels, with a Lanczos filter.
         *
         * @param int<1, 4294967295> $width
         * @param int<1, 4294967295> $height
         * @return \BernardLedit\Image\Image
         * @throws \ValueError if `$width` or `$height` is out of range.
         */
        public function resize(int $width, int $height): \BernardLedit\Image\Image {}
    }

    /**
     * Compresses an image as a JPEG, scaled down to fit `$max_width` and `$max_height`.
     *
     * @param string $data
     * @param int<5, 100> $quality
     * @param int<1, 4294967295>|null $max_width
     * @param int<1, 4294967295>|null $max_height
     * @param bool|null $optimize
     * @return array{0: string, 1: int, 2: int} `[$bytes, $width, $height]`.
     * @throws \ValueError if `$quality`, `$max_width` or `$max_height` is out of range.
     * @throws \BernardLedit\Image\ImageException if `$data` isn't an identifiable image, can't be decoded or encoded, or the output isn't smaller.
     */
    function compressToJpeg(string $data, int $quality = 85, ?bool $optimize = null, ?int $max_width = null, ?int $max_height = null): array {}

    /**
     * `BernardLedit\Image\decode(string $data): Image`
     *
     * @param string $data
     * @return \BernardLedit\Image\Image
     * @throws \BernardLedit\Image\ImageException if `$data` isn't an identifiable image, or can't be decoded.
     */
    function decode(string $data): \BernardLedit\Image\Image {}

    /**
     * `BernardLedit\Image\guessFormat(string $data): string`
     *
     * @param string $data
     * @return string
     * @throws \BernardLedit\Image\ImageException if `$data` isn't an identifiable image.
     */
    function guessFormat(string $data): string {}
}

namespace BernardLedit\Pdf {
    class PdfBitmap {
        public function __construct() {}

        /**
         * Debug info.
         *
         * @return array<string, string>
         */
        public function __debugInfo(): array {}

        /**
         * String rep.
         *
         * @return string
         */
        public function __toString(): string {}

        /**
         * Height in pixels.
         *
         * @return int
         */
        public function getHeight(): int {}

        /**
         * Width in pixels.
         *
         * @return int
         */
        public function getWidth(): int {}

        /**
         * Raw RGBA8 pixel buffer (row-major, 4 bytes per pixel).
         *
         * @return string
         */
        public function toBytes(): string {}

        /**
         * Encode the bitmap as a PNG byte stream.
         *
         * @return string
         * @throws \BernardLedit\Image\ImageException if PNG encoding fails.
         */
        public function toPngBytes(): string {}
    }

    class PdfDocument {
        /**
         * Create a new instance from bytes.
         *
         * @param string $data
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't be loaded, or `$data` isn't a PDF it can read.
         */
        public function __construct(string $data) {}

        /**
         * Appends a JPEG as a page of the document.
         *
         * @param string $jpeg
         * @return void
         * @throws \BernardLedit\Image\ImageException if `$jpeg` is invalid, truncated or in an unsupported color space.
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't append the page.
         * @throws \Exception if the document is closed.
         */
        public function appendJpegPage(string $jpeg): void {}

        /**
         * Append multiple JPEG pages to the document.
         *
         * @param list<string> $jpegs
         * @return void
         * @throws \BernardLedit\Image\ImageException if one of `$jpegs` is invalid, truncated or in an unsupported color space.
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't append a page.
         * @throws \Exception if the document is closed.
         */
        public function appendMultipleJpegPages(array $jpegs): void {}

        /**
         * Returns the PDF bytes (PHP writes files itself and keeps I/O out of Rust).
         *
         * @return string
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't save the document.
         * @throws \Exception if the document is closed.
         */
        public function asBytes(): string {}

        /**
         * Close the document. Closing it again does nothing.
         *
         * @return void
         */
        public function close(): void {}

        /**
         * Recompresses pages that have no text layer (scans) as JPEG.
         * Returns `null` when there is nothing to gain (e.g. text-only documents).
         *
         * @param int<5, 100> $quality
         * @param bool $optimize Optimize JPEG Huffman tables.
         * @param float $dpi A finite number between 1 and 1200.
         * @return \BernardLedit\Pdf\PdfDocument|null
         * @throws \ValueError if `$quality` or `$dpi` is out of range.
         * @throws \BernardLedit\Image\ImageException if JPEG encoding fails.
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't render a page, or build the new document.
         * @throws \Exception if the document is closed.
         */
        public function compressToJpeg(int $quality = 85, bool $optimize = true, float $dpi = 150.0): ?\BernardLedit\Pdf\PdfDocument {}

        /**
         * `PdfDocument::create()` empty document (new is reserved).
         *
         * @return \BernardLedit\Pdf\PdfDocument
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't be loaded, or can't create a document.
         */
        public static function create(): \BernardLedit\Pdf\PdfDocument {}

        /**
         * Return the specified page.
         *
         * @param int<0, 65535> $index
         * @return \BernardLedit\Pdf\PdfPage
         * @throws \ValueError if `$index` is out of range, or there's no page `$index`.
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't load the page.
         * @throws \Exception if the document is closed.
         */
        public function getPage(int $index): \BernardLedit\Pdf\PdfPage {}

        /**
         * Returns the page count.
         *
         * @return int
         * @throws \ValueError if the document has more than 65535 pages.
         * @throws \Exception if the document is closed.
         */
        public function getPageCount(): int {}

        /**
         * Retrieves the entire text from a document as a string.
         *
         * @return string
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't read a page's text.
         * @throws \Exception if the document is closed.
         */
        public function getText(): string {}

        /**
         * Returns true if the document has no content.
         *
         * @return bool
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't read a page.
         * @throws \Exception if the document is closed.
         */
        public function hasNoContent(): bool {}

        /**
         * Check if the document has any text.
         *
         * @return bool
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't read a page.
         * @throws \Exception if the document is closed.
         */
        public function hasText(): bool {}

        /**
         * Append pages `indexes` of `$src` to `$this`.
         *
         * @param \BernardLedit\Pdf\PdfDocument $src
         * @param list<int<0, 65535>> $indexes
         * @return void
         * @throws \ValueError if an index is out of range, `$src` is this document, or `$src` has no page at one of `$indexes`.
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't copy the pages.
         * @throws \Exception if either document is closed.
         */
        public function importPages(\BernardLedit\Pdf\PdfDocument $src, array $indexes): void {}

        /**
         * Rasterizes a page from a given index into an image, at 72 DPI.
         *
         * @param int<0, 65535> $index
         * @return \BernardLedit\Image\Image
         * @throws \ValueError if `$index` is out of range, or there's no page `$index`.
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't render the page.
         * @throws \Exception if the document is closed.
         */
        public function rasterizePage(int $index): \BernardLedit\Image\Image {}
    }

    class PdfPage {
        public function __construct() {}

        /**
         * Returns the characters on the page.
         *
         * @return list<\BernardLedit\Pdf\TextChar>
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't read the page's characters.
         * @throws \Exception if the document is closed.
         */
        public function getChars(): array {}

        /**
         * Index of the page.
         *
         * @return int
         */
        public function getIndex(): int {}

        /**
         * Returns the text of the page.
         *
         * @return string
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't read the page's text.
         * @throws \Exception if the document is closed.
         */
        public function getText(): string {}

        /**
         * Renders the page, at 72 DPI per unit of `$scale`.
         *
         * @param float $scale A finite number between 0.01 and 16.
         * @return \BernardLedit\Pdf\PdfBitmap
         * @throws \ValueError if `$scale` is out of range.
         * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't render the page.
         * @throws \Exception if the document is closed.
         */
        public function renderToBitmap(float $scale = 1.0): \BernardLedit\Pdf\PdfBitmap {}
    }

    /**
     * Text character representation.
     */
    class TextChar {
        /**
         * @param string $char Only the first character is kept; `" "` if empty.
         * @param string $font_name
         * @param float $font_size
         * @param int<0, 4294967295> $font_weight
         * @param array{int<0, 255>, int<0, 255>, int<0, 255>, int<0, 255>}|null $stroke_color RGBA.
         * @param array{int<0, 255>, int<0, 255>, int<0, 255>, int<0, 255>}|null $fill_color RGBA.
         * @param int<-2147483648, 2147483647> $font_flags
         * @param array{float, float, float, float}|null $bounds Zeros if `null`.
         * @throws \ValueError if `$font_weight` or `$font_flags` is out of range, or `$stroke_color`, `$fill_color` or `$bounds` doesn't have 4 values, or a color value is out of range.
         */
        public function __construct(string $char, string $font_name, float $font_size, int $font_weight, ?array $stroke_color, ?array $fill_color, int $font_flags, ?array $bounds = null) {}

        /**
         * Default debug info.
         *
         * @return array<string, string>
         */
        public function __debugInfo(): array {}

        /**
         * String rep.
         *
         * @return string
         */
        public function __toString(): string {}
    }

    /**
     * Keeps or removes pages, if the PDF has at least `$on_min_pages` pages. Negative indexes count
     * from the end, and indexes past the end are skipped.
     *
     * @param string $data
     * @param string $operation `KEEP_ONLY` or `REMOVE`, case-insensitive.
     * @param int<0, 4294967295> $on_min_pages
     * @param list<int<-2147483648, 2147483647>> $page_indexes
     * @return array{0: \BernardLedit\Pdf\PdfDocument|null, 1: list<int>} The new document, or `null` if the PDF has fewer than `$on_min_pages` pages; and the skipped indexes.
     * @throws \ValueError if `$on_min_pages` or an index is out of range, `$operation` isn't `KEEP_ONLY` or `REMOVE`, the PDF has no content, or no pages would be left.
     * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't be loaded, `$data` isn't a PDF it can read, or it can't build the new document.
     */
    function applyPageOptions(string $data, string $operation, int $on_min_pages, array $page_indexes): array {}

    /**
     * Compresses PDF bytes. Returns the input unchanged if it can't be made smaller.
     *
     * @param string $data
     * @param int<5, 100> $quality
     * @param bool|null $optimize Whether to optimize JPEG Huffman tables; defaults to `true`.
     * @param float|null $dpi A finite number between 1 and 1200; 150 if `null`.
     * @return string
     * @throws \ValueError if `$quality` or `$dpi` is out of range.
     * @throws \BernardLedit\Image\ImageException if JPEG encoding fails.
     * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't be loaded, `$data` isn't a PDF it can read, or it can't render a page or build the new document.
     */
    function compressToJpeg(string $data, int $quality = 85, ?bool $optimize = null, ?float $dpi = null): string {}

    /**
     * Builds one new document per group of 0-indexed page numbers.
     * `data` is a PDF or an image (treated as a one-page document).
     *
     * @param string $data
     * @param non-empty-list<non-empty-list<int<0, 65535>>> $page_indexes
     * @return list<\BernardLedit\Pdf\PdfDocument> One document per group, in the same order.
     * @throws \ValueError if an index is out of range, `$page_indexes` or one of its groups is empty, or there's no page at one of the indexes.
     * @throws \BernardLedit\Image\ImageException if `$data` is neither a PDF nor an identifiable, decodable image.
     * @throws \BernardLedit\Pdf\PdfiumException if `PDFium` can't be loaded, `$data` is a PDF it can't read, or it can't build the documents.
     */
    function extractFromPageRanges(string $data, array $page_indexes): array {}

    /**
     * Drops every byte before the first `%PDF-` marker.
     *
     * @param string $data
     * @param int<0, max> $maximum_offset How far into `$data` to look for the marker, in bytes.
     * @return string
     * @throws \ValueError if `$maximum_offset` is negative, or there's no marker before it.
     */
    function fixPdf(string $data, int $maximum_offset = 500): string {}
}

namespace BernardLedit\Image {
    /**
     * Input that isn't an identifiable or decodable image, and encoding failures.
     */
    class ImageException extends \Exception {}
}

namespace BernardLedit\Pdf {
    /**
     * `PDFium` can't be loaded, or fails to read, render or write a document.
     */
    class PdfiumException extends \Exception {}
}
