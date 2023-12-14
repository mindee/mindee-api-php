#!/usr/bin/env php
<?php

namespace Mindee\CLI;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/CustomArg.php';

use Mindee\Product;


if ($argc < 2) {
    echo "Usage: php mindee.php <arg1> <arg2> ...\n";
    exit(1);
}


class CommandConfig
{
    public function __construct($help, $doc_class, $is_sync, $is_async = false)
    {
        // Constructor implementation
    }
}

$documents = [
    "barcodeReader" => new CommandConfig(
        "Barcode-reader tool",
        Product\BarcodeReader\BarcodeReaderV1::class,
        true
    ),
    "cropper" => new CommandConfig(
        "Cropper tool",
        Product\Cropper\CropperV1::class,
        true
    ),
    "custom" => new CommandConfig(
        "Custom document type from API builder",
        Product\Custom\CustomV1::class,
        true,
        false
    ),
    "euLicensePlate" => new CommandConfig(
        "EU License Plate",
        Product\Eu\LicensePlate\LicensePlateV1::class,
        true
    ),
    "financialDocument" => new CommandConfig(
        "Financial Document (receipt or invoice)",
        Product\FinancialDocument\FinancialDocumentV1::class,
        true
    ),
    "frBankAccountDetails" => new CommandConfig(
        "FR Bank Account Details",
        Product\Fr\BankAccountDetails\BankAccountDetailsV2::class,
        true
    ),
    "frCarteGrise" => new CommandConfig(
        "FR Carte Grise",
        Product\Fr\CarteGrise\CarteGriseV1::class,
        true
    ),
    "frIdCard" => new CommandConfig(
        "FR ID Card",
        Product\Fr\IdCard\IdCardV2::class,
        true
    ),
    "invoice" => new CommandConfig(
        "Invoice",
        Product\Invoice\InvoiceV4::class,
        true
    ),
    "invoiceSplitter" => new CommandConfig(
        "Invoice Splitter",
        Product\InvoiceSplitter\InvoiceSplitterV1::class,
        false,
        true
    ),
    "multiReceipts" => new CommandConfig(
        "Multi-receipts detector",
        Product\MultiReceiptsDetector\MultiReceiptsDetectorV1::class,
        true
    ),
    "passport" => new CommandConfig(
        "Passport",
        Product\Passport\PassportV1::class,
        true
    ),
    "proofOfAddress" => new CommandConfig(
        "Proof of Address",
        Product\ProofOfAddress\ProofOfAddressV1::class,
        true
    ),
    "receipt" => new CommandConfig(
        "Expense Receipt",
        Product\Receipt\ReceiptV5::class,
        true
    ),
    "usBankCheck" => new CommandConfig(
        "US Bank Check",
        Product\Us\BankCheck\BankCheckV1::class,
        true
    ),
    "usDriverLicense" => new CommandConfig(
        "US Driver License",
        Product\Us\DriverLicense\DriverLicenseV1::class,
        true
    ),
    "usW9" => new CommandConfig(
        "US W9",
        Product\Us\W9\W9V1::class,
        true
    ),
];

$options = getopt("a:b:c", ["arg1:", "arg2:", "help"]);

$shortopts = "";
$shortopts .= "A";  // Asynchronous parsing (optional)
$shortopts .= "t"; // Include all words (optional)
$shortopts .= "o::";   // Output type [<summary>,raw,parsed] (optional)
$shortopts .= "i::";   // Input type [<path>,file,base64, bytes, url] (optional)
$longopts = [
    "arg1:",      // Requires an argument
    "arg2::",     // Optional argument
    "help",       // No argument
];

$argsCatalog = [
    "A" => new CustomArg(
        "asynchronous",
        "Enable asynchronous parsing.",
        "parse_async",
        false
    ),
    "k:" => new CustomArg(
        "key",
        "Include 'all words' option.",
        "parse_ocr",
        false
    ),
    "t" => new CustomArg(
        "full-text",
        "Include 'all words' option.",
        "parse_ocr",
        false
    ),
    "o::" => new CustomArg(
        "output-type",
        "Output type (optional)\n" .
        "- summary: a basic summary (default)\n" .
        "- raw: the raw HTTP response\n" .
        "- parsed: the validated and parsed data fields",
        "output_type",
        false,
        ["summary", "raw", "parsed"],
        "summary"
    ),
    "i::" => new CustomArg(
        "input-type",
        "Input type (optional)\n" .
        "- path: open a path (default).\n" .
        "- file: open as a file handle.\n" .
        "- base64: open a base64 encoded text file.\n" .
        "- bytes: open the contents as raw bytes.\n" .
        "- url: open an URL.",
        "input_type",
        false,
        ["path", "base64", "bytes", "url"],
        "path"
    ),
    "p:" => new CustomArg(
        "keep-pages",
        "Cut document pages.",
        "doc_pages",
        false,
        null,
        5
    ),
    "c" => new CustomArg(
        "cut-pages",
        "Cut document pages.",
        "cut_doc",
        false
    ),
    "a" => new CustomArg(
        "account",
        "API account name for the endpoint (required).",
        "account_name",
        true
    ),
    "e" => new CustomArg(
        "endpoint",
        "API endpoint name (required).",
        "endpoint_name",
        true
    ),
    "v" => new CustomArg(
        "version",
        "Version for the endpoint. If not set, use the latest version of the model.",
        "api_version",
        false,
        null,
        "1"
    ),
];


$options = getopt("", array_keys($argsCatalog));

foreach ($options as $opt => $value) {
    echo "Option '$opt' value: ";
    var_dump($value);
    if (isset($descriptors[$opt])) {
        echo "Description: " . $descriptors[$opt] . "\n";
    }
}
foreach (array_slice($argv, 1) as $arg => $value) {
    echo "Option '$arg' value: ";
    var_dump($value);
}
