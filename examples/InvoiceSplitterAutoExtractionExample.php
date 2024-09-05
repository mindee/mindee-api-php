<?php

use Mindee\Client;
use Mindee\Extraction\PdfExtractor;
use Mindee\Input\PathInput;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;
use Mindee\Product\Invoice\InvoiceV4;

$apiKey = "my-api-key-here";
$mindeeClient = new Client($apiKey);

$inputPath = "path/to/your/file.ext";
$inputSource = new PathInput($inputPath);

if ($inputSource->isPdf()) {
    $pdfExtractor = new PdfExtractor($inputSource);
    if ($pdfExtractor->getPageCount() > 1) {
        $invoiceSplitterResponse = $mindeeClient->enqueueAndParse(
            InvoiceSplitterV1::class,
            $inputSource,
        );
        $pageGroups = $invoiceSplitterResponse->document->inference->prediction->invoicePageGroups;
        $extractedPdfs = $pdfExtractor->extractInvoices($pageGroups);

        foreach ($extractedPdfs as $extractedPdf) {
            // Optional: Save the files locally
            // $extractedPdf->writeToFile("output/path"):

            $invoiceResult = $mindeeClient->parse(
                InvoiceV4::class,
                $extractedPdf->asInputSource()
            );
            echo $invoiceResult->document;
        }
    } else {
        $invoiceResult = $mindeeClient->parse(InvoiceV4::class, $inputSource);
        echo $invoiceResult->document;
    }
} else {
    $invoiceResult = $mindeeClient->parse(InvoiceV4::class, $inputSource);
    echo $invoiceResult->document;
}
