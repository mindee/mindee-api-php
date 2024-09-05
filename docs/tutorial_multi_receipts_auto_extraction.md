# Multi Receipts Auto-Extraction (PHP)

This tutorial demonstrates how to use the Mindee library for automatic multi-receipts extraction in PHP.
A full version of the script detailed in this tutorial is
available [here](https://github.com/mindee/mindee-api-php/blob/main/examples/InvoiceSplitterAutoExtractionExample.php).
The process involves handling both single-page and multi-page PDF invoices, as well as other file formats.

## Prerequisites

* [ImageMagick](https://www.php.net/manual/en/imagick.setup.php)
* [GhostScript](https://www.ghostscript.com/)
* A working subscription to
  the [Multi-Receipts Detector API](https://platform.mindee.com/mindee/multi_receipts_detector/live-interface)
* A working subscription to
  either [Receipts](https://platform.mindee.com/mindee/expense_receipts/live-interface)
  or [Financial Document](https://platform.mindee.com/mindee/financial_document/live-interface), depending on your
  implementation.

## Basic Setup

Start by importing the necessary classes and set up the Mindee client:

```php
use Mindee\Client;
use Mindee\Extraction\ImageExtractor;
use Mindee\Input\PathInput;
use Mindee\Product\MultiReceiptsDetector\MultiReceiptsDetectorV1;
use Mindee\Product\Receipt\ReceiptV5;

$apiKey = "my-api-key-here";
$mindeeClient = new Client($apiKey);
```

## Processing the Input



##### Create an input source from the file path:

```php
$inputPath = "path/to/your/file.ext";
$inputSource = new PathInput($inputPath);
```

Set up the image extractor:

```php
$imageExtractor = new ImageExtractor($inputSource);
```

## Multi-Receipt Detection
##### Use the Multi-Receipts Detector API to identify receipts in the document:

```php
$multiReceiptsResult = $mindeeClient->parse(MultiReceiptsDetectorV1::class, $inputSource);
```

## Handling Multi-Page Documents
##### Determine the number of pages in the document:

```php
$pageCount = 1;

if ($inputSource->isPDF()) {
    $pageCount = $inputSource->countDocPages();
}
```

## Extracting Individual Receipts
##### Iterate through each page and extract the receipts:

```php
$totalExtractedReceipts = [];

for ($i = 0; $i < $pageCount; $i++) {
    $receiptsPositions = $multiReceiptsResult->document->inference->pages[$i]->prediction->receipts;
    $extractedReceipts = $imageExtractor->extractImagesFromPage($receiptsPositions, $i);
    $totalExtractedReceipts = array_merge($totalExtractedReceipts, $extractedReceipts);
}
```

## Processing Individual Receipts
##### For each extracted receipt, parse it using the Receipt API:

```php
foreach ($totalExtractedReceipts as $receipt) {
    // Optional: save the extracted receipts to a file
    // $receipt->writeToFile("output/path");

    $result = $mindeeClient->parse(ReceiptV5::class, $receipt->asInputSource());
    echo $result->document;
}
```