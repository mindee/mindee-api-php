<?php

// Stubs for bernard_ledit

namespace BernardLedit\Image {
    class Image {
        /**
         * Decodes an image from its encoded bytes: `new Image(string $data)`.
         *
         * @param string $data
         * @throws \Exception Throws if the data is not a supported image format.
         * @throws \Exception Throws if the image cannot be decoded.
         */
        public function __construct(string $data) {}

        /**
         * Crops a rendered page's image from a given bounding box.
         *
         * @param int $left
         * @param int $top
         * @param int $right
         * @param int $bottom
         * @return \BernardLedit\Image\Image
         * @throws \Exception Returns an error if the cropping operation fails.
         */
        public function crop(int $left, int $top, int $right, int $bottom): \BernardLedit\Image\Image {}

        /**
         * Returns the encoded bytes as a PHP string.
         *
         * @param string $format
         * @param int $quality
         * @param bool $optimize
         * @return string
         * @throws \Exception Returns an error if the encoding operation fails.
         */
        public function encode(string $format, int $quality = 85, bool $optimize = true): string {}

        /**
         * Upper-case format name (`"JPEG"`, `"PNG"`, etc.) or `null`.
         *
         * @return string|null
         * @throws \Exception Returns an error if the format name cannot be converted to a string.
         */
        public function getFormat(): ?string {}

        /**
         * Height.
         *
         * @return int
         */
        public function getHeight(): int {}

        /**
         * Dimensions as `[width, height]`.
         *
         * @return array
         */
        public function getSize(): array {}

        /**
         * Width.
         *
         * @return int
         */
        public function getWidth(): int {}

        /**
         * Resizes an image using dimensions and a filter.
         *
         * @param int $width
         * @param int $height
         * @return \BernardLedit\Image\Image
         * @throws \Exception Returns an error if the resizing operation fails.
         */
        public function resize(int $width, int $height): \BernardLedit\Image\Image {}
    }

    /**
     * `BernardLedit\Image\ImageException extends \Exception` Decode and encode failures.
     */
    class ImageException extends \Exception {
        public function __construct() {}
    }

    /**
     * `BernardLedit\Image\ImageExtractException extends \Exception` Decode and encode failures.
     */
    class ImageExtractException extends \Exception {
        public function __construct() {}
    }

    /**
     * Compresses an image.
     *
     * @param string $data
     * @param int $quality
     * @param int|null $max_width
     * @param int|null $max_height
     * @param bool|null $optimize
     * @return array
     * @throws \Exception Returns an error if the compression operation fails, or if the resulting dimensions
     * @throws \Exception don't fit in a 32-bit integer.
     */
    function compress(string $data, int $quality = 85, ?int $max_width = null, ?int $max_height = null, ?bool $optimize = null): array {}

    /**
     * `BernardLedit\Image\decode(string $data): Image`
     *
     * @param string $data
     * @return \BernardLedit\Image\Image
     * @throws \Exception Returns an error if the decoding operation fails.
     */
    function decode(string $data): \BernardLedit\Image\Image {}

    /**
     * Extracts polygons from an image.
     *
     * @param string $data
     * @param int $page_id
     * @param array $polygons
     * @param float $dpi
     * @return array
     * @throws \Exception Return an error if the image cannot be decoded or if the polygons cannot be extracted.
     */
    function extractFromPolygons(string $data, int $page_id, array $polygons, float $dpi = 72.0): array {}

    /**
     * `BernardLedit\Image\guessFormat(string $data): string`
     *
     * @param string $data
     * @return string
     * @throws \Exception Returns an error if the decoding operation fails.
     */
    function guessFormat(string $data): string {}
}

namespace BernardLedit\Pdf {
    class PdfDocument {
        /**
         * Create a new instance from bytes.
         *
         * @param string $data
         * @throws \Exception Returns a PHP Exception if the creation fails.
         */
        public function __construct(string $data) {}

        /**
         * Append text to a page.
         *
         * @param int $page_idx
         * @param array $chars
         * @return void
         * @throws \Exception Returns a PHP Exception if the text append fails.
         */
        public function addText(int $page_idx, array $chars): void {}

        /**
         * Appends a sequence of JPEG pages as pages to the document.
         *
         * @param string $jpeg
         * @return void
         * @throws \Exception Returns a PHP Exception if the append fails.
         */
        public function appendJpegPage(string $jpeg): void {}

        /**
         * Append multiple JPEG pages to the document.
         *
         * @param array $jpegs
         * @return void
         * @throws \Exception Returns a PHP Exception if the append fails.
         */
        public function appendMultipleJpegPages(array $jpegs): void {}

        /**
         * Returns the PDF bytes (PHP writes files itself and keeps I/O out of Rust).
         *
         * @return string
         * @throws \Exception Returns a PHP Exception if the save fails.
         */
        public function asBytes(): string {}

        /**
         * Close the document.
         *
         * @return void
         * @throws \Exception Returns a PHP Exception if the close fails.
         */
        public function close(): void {}

        /**
         * Recompresses pages that have no text layer (scans) as JPEG.
         * Returns `null` when there is nothing to gain (e.g. text-only documents).
         *
         * @param int $quality
         * @param bool $optimize
         * @param float $dpi
         * @return \BernardLedit\Pdf\PdfDocument|null
         * @throws \Exception Returns a PHP Exception if rendering or encoding fails.
         */
        public function compress(int $quality = 85, bool $optimize = true, float $dpi = 150.0): ?\BernardLedit\Pdf\PdfDocument {}

        /**
         * `PdfDocument::create()` empty document (new is reserved).
         *
         * @return \BernardLedit\Pdf\PdfDocument
         * @throws \Exception Returns a PHP Exception if the creation fails.
         */
        public static function create(): \BernardLedit\Pdf\PdfDocument {}

        /**
         * Return the specified page.
         *
         * @param int $index
         * @return \BernardLedit\Pdf\PdfPage
         * @throws \Exception Returns a PHP Exception if the page cannot be retrieved.
         */
        public function getPage(int $index): \BernardLedit\Pdf\PdfPage {}

        /**
         * Returns the page count.
         *
         * @return int
         * @throws \Exception Returns a PHP Exception if the count fails.
         */
        public function getPageCount(): int {}

        /**
         * Retrieves the entire text from a document as a string.
         *
         * @return string
         * @throws \Exception Returns a PHP Exception if the text retrieval fails.
         */
        public function getText(): string {}

        /**
         * Returns true if the document has no content.
         *
         * @return bool
         * @throws \Exception Returns a PHP Exception if the check fails.
         */
        public function hasNoContent(): bool {}

        /**
         * Check if the document has any text.
         *
         * @return bool
         * @throws \Exception Returns a PHP Exception if the check fails.
         */
        public function hasText(): bool {}

        /**
         * Append pages `indexes` of `$src` to `$this`.
         *
         * @param \BernardLedit\Pdf\PdfDocument $src
         * @param array $indexes
         * @return void
         * @throws \Exception Returns a PHP Exception if the import fails.
         */
        public function importPages(\BernardLedit\Pdf\PdfDocument $src, array $indexes): void {}

        /**
         * Rasterizes a page from a given index as a JPEG.
         *
         * @param int $index
         * @param int $quality
         * @return string
         * @throws \Exception Returns a PHP Exception if the rasterization fails.
         */
        public function rasterizePageToJpegBytes(int $index, int $quality): string {}
    }

    class PdfExtractException extends \Exception {
        public function __construct() {}
    }

    class PdfPage {
        public function __construct() {}

        /**
         * Returns the characters on the page.
         *
         * @return array
         * @throws \Exception Returns a PHP Exception if the `PdfDocument` is inaccessible.
         */
        public function getChars(): array {}

        /**
         * Index of the page.
         *
         * @return int
         */
        public function getIndex(): int {}

        /**
         * `[width, height]` in points.
         *
         * @return array
         * @throws \Exception Returns a PHP Exception if the `PdfDocument` is closed.
         */
        public function getSize(): array {}

        /**
         * Returns the text of the page.
         *
         * @return string
         * @throws \Exception Returns a PHP Exception if the `PdfDocument` is inaccessible.
         */
        public function getText(): string {}

        /**
         * Checks whether the entire document is empty.
         *
         * @return bool
         * @throws \Exception Returns a PHP Exception if the `PdfDocument` is inaccessible.
         */
        public function isEmpty(): bool {}

        /**
         * Renders the page.
         *
         * @param float $scale
         * @return \PdfBitmap
         * @throws \Exception Returns a PHP Exception if the `PdfDocument` is inaccessible.
         */
        public function renderToBitmap(float $scale = 1.0): \PdfBitmap {}
    }

    class PdfiumException extends \Exception {
        public function __construct() {}
    }

    /**
     * Keeps or removes pages. Negative indexes count from the end.///
     *
     * @param string $data
     * @param string $operation
     * @param int $on_min_pages
     * @param array $page_indexes
     * @return array
     * @throws \Exception Throws a [`ValueError`] if one or more of the provided values is incorrect.
     */
    function applyPageOptions(string $data, string $operation, int $on_min_pages, array $page_indexes): array {}

    /**
     * Compresses PDF bytes. Returns the input unchanged if it can't be made smaller.
     *
     * @param string $data
     * @param int $quality
     * @param bool|null $optimize
     * @param float|null $dpi
     * @return string
     * @throws \Exception Returns a PHP Exception if the PDF is invalid or compression fails.
     */
    function compress(string $data, int $quality = 85, ?bool $optimize = null, ?float $dpi = null): string {}

    /**
     * Builds one new document per group of 0-indexed page numbers.
     * `data` is a PDF or an image (treated as a one-page document).
     *
     * @param string $data
     * @param array $page_indexes
     * @return array
     * @throws \Exception Throws if no groups are given.
     * @throws \Exception Throws if a group is empty.
     * @throws \Exception Throws if a page index is out of range.
     * @throws \Exception Throws if the data is neither a valid PDF nor a supported image.
     */
    function extractFromPageRanges(string $data, array $page_indexes): array {}

    /**
     * Drops every byte before the first `%PDF-` marker.
     *
     * @param string $data
     * @param int $maximum_offset
     * @return string
     * @throws \Exception Throws a [`ValueError`] if there is no marker before `$maximumOffset`.
     */
    function fixPdf(string $data, int $maximum_offset = 500): string {}
}

namespace {
    class PdfBitmap {
        public function __construct() {}

        /**
         * Debug info.
         *
         * @return array
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
         * @throws \Exception Returns a PHP Exception if the conversion fails.
         */
        public function toPngBytes(): string {}
    }

    /**
     * Text character representation.
     */
    class TextChar {
        /**
         * Create a new text character.
         *
         * @param string $char
         * @param string $font_name
         * @param float $font_size
         * @param int $font_weight
         * @param array|null $stroke_color
         * @param array|null $fill_color
         * @param int $font_flags
         * @param array|null $bounds
         * @throws \Exception Throws a `ValueError` if `stroke_color` or `fill_color` is provided
         * @throws \Exception with a length other than 4, or if `bounds` is provided with a length
         * @throws \Exception other than 4.
         */
        public function __construct(string $char, string $font_name, float $font_size, int $font_weight, ?array $stroke_color, ?array $fill_color, int $font_flags, ?array $bounds = null) {}

        /**
         * Default debug info.
         *
         * @return array
         */
        public function __debugInfo(): array {}

        /**
         * String rep.
         *
         * @return string
         */
        public function __toString(): string {}
    }
}
