<?php

use Mindee\Client;
use Mindee\Extraction\ImageExtractor;
use Mindee\Input\PathInput;
use Mindee\Product\MultiReceiptsDetector\MultiReceiptsDetectorV1;
use Mindee\Product\Receipt\ReceiptV5;

$apiKey = "my-api-key-here";
$mindeeClient = new Client($apiKey);

$inputPath = "path/to/your/file.ext";
$inputSource = new PathInput($inputPath);

$imageExtractor = new ImageExtractor($inputSource);

$multiReceiptsResult = $mindeeClient->parse(MultiReceiptsDetectorV1::class, $inputSource);
$pageCount = 1;

if ($inputSource->isPDF()) {
    $pageCount = $inputSource->countDocPages();
}

$totalExtractedReceipts = [];

for ($i = 0; $i < $pageCount; $i++) {
    $receiptsPositions = $multiReceiptsResult->document->inference->pages[$i]->prediction->receipts;
    $extractedReceipts = $imageExtractor->extractImagesFromPage($receiptsPositions, $i);
    $totalExtractedReceipts = array_merge($totalExtractedReceipts, $extractedReceipts);
}

foreach ($totalExtractedReceipts as $receipt) {
    // $receipt->writeToFile("output/path"); // Optional: save the extracted receipts to a file.
    $result = $mindeeClient->parse(ReceiptV5::class, $receipt->asInputSource());
    echo $result->document;
}
