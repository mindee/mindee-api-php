<?php

use Mindee\Client;
use Mindee\Extraction\ImageExtractor;
use Mindee\Input\PathInput;
use Mindee\Product\MultiReceiptsDetector\MultiReceiptsDetectorV1;
use Mindee\Product\Receipt\ReceiptV5;

$mindeeClient = new Client("my-api-key-here");
// $mindeeClient = new Client(); // Optionally, use an environment variable.
$inputPath = "path/to/your/file.ext";


function processReceipts($client, $inputPath) {
    $inputSource = new PathInput($inputPath);
    $imageExtractor = new ImageExtractor($inputSource);

    $multiReceiptsResult = $client->parse(MultiReceiptsDetectorV1::class, $inputSource);
    $pageCount = $inputSource->getPageCount();

    $totalExtractedReceipts = [];

    for ($i = 0; $i < $pageCount; $i++) {
        $receiptsPositions = $multiReceiptsResult->document->inference->pages[$i]->prediction->receipts;
        $extractedReceipts = $imageExtractor->extractImagesFromPage($receiptsPositions, $i);
        $totalExtractedReceipts = array_merge($totalExtractedReceipts, $extractedReceipts);
    }

    foreach ($totalExtractedReceipts as $receipt) {
        // Optional: save the extracted receipts to a file
        // $receipt->writeToFile("output/path");

        $result = $client->parse(ReceiptV5::class, $receipt->asInputSource());
        echo $result->document . "\n";
    }
}
processReceipts($mindeeClient, $inputPath);
