<?php

use Mindee\Client;
use Mindee\Extraction\PdfExtractor;
use Mindee\Input\PathInput;
use Mindee\Product\Invoice\InvoiceV4;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;

function parseInvoice(string $filePath, Client $mindeeClient)
{
    $inputSource = new PathInput($filePath);

    if ($inputSource->isPdf() && $inputSource->getPageCount() > 1) {
        parseMultiPage($inputSource, $mindeeClient);
    } else {
        parseSinglePage($inputSource, $mindeeClient);
    }
}

function parseSinglePage(PathInput $inputSource, Client $mindeeClient)
{
    $invoiceResult = $mindeeClient->parse(InvoiceV4::class, $inputSource);
    echo $invoiceResult->document;
}

function parseMultiPage(PathInput $inputSource, Client $mindeeClient)
{
    global $mindeeClient;
    $pdfExtractor = new PdfExtractor($inputSource);
    $invoiceSplitterResponse = $mindeeClient->enqueueAndParse(
        InvoiceSplitterV1::class,
        $inputSource
    );
    $pageGroups = $invoiceSplitterResponse->document->inference->prediction->invoicePageGroups;
    $extractedPdfs = $pdfExtractor->extractInvoices($pageGroups);

    foreach ($extractedPdfs as $extractedPdf) {
        // Optional: Save the files locally
        // $extractedPdf->writeToFile("output/path");

        $invoiceResult = $mindeeClient->parse(
            InvoiceV4::class,
            $extractedPdf->asInputSource()
        );
        echo $invoiceResult->document;
    }
}

$mindeeClient = new Client("my-api-key-here");
// $mindeeClient = new Client(); // Optionally, use an environment variable.
$inputPath = "path/to/your/file.ext";
parseInvoice($inputPath, $mindeeClient);
