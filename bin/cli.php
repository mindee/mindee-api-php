<?php

namespace Mindee\CLI;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/CommandConfig.php';
require __DIR__ . '/MindeeCLICommand.php';

use Mindee\Product;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;


$documents = [
    "barcode-reader" => new CommandConfig(
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
    "eu-driver-license" => new CommandConfig(
        "EU Driver License",
        Product\Eu\DriverLicense\DriverLicenseV1::class,
        true
    ),
    "eu-license-plate" => new CommandConfig(
        "EU License Plate",
        Product\Eu\LicensePlate\LicensePlateV1::class,
        true
    ),
    "financial-document" => new CommandConfig(
        "Financial Document (receipt or invoice)",
        Product\FinancialDocument\FinancialDocumentV1::class,
        true
    ),
    "fr-bank-account-details" => new CommandConfig(
        "FR Bank Account Details",
        Product\Fr\BankAccountDetails\BankAccountDetailsV2::class,
        true
    ),
    "fr-carte-grise" => new CommandConfig(
        "FR Carte Grise",
        Product\Fr\CarteGrise\CarteGriseV1::class,
        true
    ),
    "fr-carte-vitale" => new CommandConfig(
        "FR Carte Vitale",
        Product\Fr\CarteVitale\CarteVitaleV1::class,
        true
    ),
    "fr-id-card" => new CommandConfig(
        "FR ID Card",
        Product\Fr\IdCard\IdCardV2::class,
        true
    ),
    "generated" => new CommandConfig(
        "Custom document type from docTI",
        Product\Generated\GeneratedV1::class,
        false,
        true
    ),
    "international-id" => new CommandConfig(
        "International Id",
        Product\InternationalId\InternationalIdV2::class,
        false,
        true
    ),
    "invoice" => new CommandConfig(
        "Invoice",
        Product\Invoice\InvoiceV4::class,
        true
    ),
    "invoice-splitter" => new CommandConfig(
        "Invoice Splitter",
        Product\InvoiceSplitter\InvoiceSplitterV1::class,
        false,
        true
    ),
    "multi-receipts" => new CommandConfig(
        "Multi-receipts detector",
        Product\MultiReceiptsDetector\MultiReceiptsDetectorV1::class,
        true
    ),
    "passport" => new CommandConfig(
        "Passport",
        Product\Passport\PassportV1::class,
        true
    ),
    "proof-of-address" => new CommandConfig(
        "Proof of Address",
        Product\ProofOfAddress\ProofOfAddressV1::class,
        true
    ),
    "receipt" => new CommandConfig(
        "Expense Receipt",
        Product\Receipt\ReceiptV5::class,
        true
    ),
    "resume" => new CommandConfig(
        "Resume",
        Product\Resume\ResumeV1::class,
        false,
        true
    ),
    "us-bank-check" => new CommandConfig(
        "US Bank Check",
        Product\Us\BankCheck\BankCheckV1::class,
        true
    ),
    "us-driver-license" => new CommandConfig(
        "US Driver License",
        Product\Us\DriverLicense\DriverLicenseV1::class,
        true
    ),
    "us-w9" => new CommandConfig(
        "US W9",
        Product\Us\W9\W9V1::class,
        true
    ),
];

$cli = new Application();
$mindeeCommand = new MindeeCLICommand($documents);
$cli->add($mindeeCommand);
try {
    $cli->add($mindeeCommand);
    $cli->setDefaultCommand($mindeeCommand->getName(), true);
    $cli->run();
} catch (\Exception $e) {
    error_log("Could not start the Mindee CLI, an exception was raised:");
    error_log($e->getMessage());
}
