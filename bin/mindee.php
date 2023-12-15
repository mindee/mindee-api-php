#!/usr/bin/env php
<?php

namespace Mindee\CLI;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/CommandConfig.php';
require __DIR__ . '/MindeeCLI.php';
use Mindee\Product;



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


$cli = new MindeeCLI($documents);
$cli->run();
