# Invoice Splitter Auto-Extraction (PHP)

This tutorial demonstrates how to use the Mindee library for automatic invoice splitting and data extraction in PHP.
A full version of the script detailed in this tutorial is
available [here](https://github.com/mindee/mindee-api-php/blob/main/examples/InvoiceSplitterAutoExtractionExample.php).
The process involves handling both single-page and multi-page PDF invoices, as well as other file formats.

## Prerequisites

* [ImageMagick](https://www.php.net/manual/en/imagick.setup.php)
* [GhostScript](https://www.ghostscript.com/)
* A working subscription to
  the [Invoice Splitter API](https://platform.mindee.com/mindee/invoice_splitter/live-interface)
* A working subscription to
  either [Invoice](https://platform.mindee.com/mindee/invoices/live-interface), [Receipts](https://platform.mindee.com/mindee/expense_receipts/live-interface)
  or [Financial Document](https://platform.mindee.com/mindee/financial_document/live-interface), depending on your
  implementation.

## Basic setup

Start by importing the necessary classes and set up the Mindee client:

```php
use Mindee\Client;
use Mindee\Extraction\PdfExtractor;
use Mindee\Input\PathInput;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;
use Mindee\Product\Invoice\InvoiceV4;

$apiKey = "my-api-key-here";
$mindeeClient = new Client($apiKey);
```

## Processing the Input

##### Create an input source from the file path:

```php
$inputPath = "path/to/your/file.ext";
$inputSource = new PathInput($inputPath);
```

##### Check if the file is a PDF:

```php
if ($inputSource->isPdf()) {
    // PDF processing
} else {
    // Non-PDF processing
}
```

> Though not necessarily mandatory, this check will avoid needless calls to the Invoice Splitter API.

##### Setting up the extractor:

Most of the internal logic is handled through the `PdfExtractor` class, which is simply instanciated like so:

```php
$pdfExtractor = new PdfExtractor($inputSource);
```

Like with the format checking before, we can check whether this pdf file only has one page, in which case, no need to
send it to the Invoice Splitter API:

```php
if ($pdfExtractor->getPageCount() > 1)
{
    foreach ($extractedPdfs as $extractedPdf) {
    // Optional: Save the files locally
    // $extractedPdf->writeToFile("output/path"):

    $invoiceResult = $mindeeClient->parse(
        InvoiceV4::class,
        $extractedPdf->asInputSource()
    );
    echo $invoiceResult->document;
} else {
    $invoiceResult = $mindeeClient->parse(InvoiceV4::class, $inputSource);
    echo $invoiceResult->document;
}
```

This feature is of course also compatible with
the [financial document](https://platform.mindee.com/mindee/financial_document/live-interface)
and [receipt](https://platform.mindee.com/mindee/expense_receipts/live-interface) APIs instead of invoice.
