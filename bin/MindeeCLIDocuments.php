<?php

namespace Mindee\CLI;

/**
 * Document specifications for CLI usage.
 */
class MindeeCLIDocuments
{
    /**
     * @return array Specifications for each Mindee Document, for CLI usage.
     */
    public static function getSpecs(): array
    {
        require __DIR__ . '/DocumentCommandConfig.php';
        return [
            "barcode-reader" => new DocumentCommandConfig(
                "Barcode-reader tool",
                \Mindee\Product\BarcodeReader\BarcodeReaderV1::class,
                true
            ),
            "cropper" => new DocumentCommandConfig(
                "Cropper tool",
                \Mindee\Product\Cropper\CropperV1::class,
                true
            ),
            "custom" => new DocumentCommandConfig(
                "Custom document type from API builder",
                \Mindee\Product\Custom\CustomV1::class,
                true,
                false
            ),
            "eu-driver-license" => new DocumentCommandConfig(
                "EU Driver License",
                \Mindee\Product\Eu\DriverLicense\DriverLicenseV1::class,
                true
            ),
            "eu-license-plate" => new DocumentCommandConfig(
                "EU License Plate",
                \Mindee\Product\Eu\LicensePlate\LicensePlateV1::class,
                true
            ),
            "financial-document" => new DocumentCommandConfig(
                "Financial Document (receipt or invoice)",
                \Mindee\Product\FinancialDocument\FinancialDocumentV1::class,
                true,
                true
            ),
            "fr-bank-account-details" => new DocumentCommandConfig(
                "FR Bank Account Details",
                \Mindee\Product\Fr\BankAccountDetails\BankAccountDetailsV2::class,
                true
            ),
            "fr-carte-grise" => new DocumentCommandConfig(
                "FR Carte Grise",
                \Mindee\Product\Fr\CarteGrise\CarteGriseV1::class,
                true
            ),
            "fr-carte-vitale" => new DocumentCommandConfig(
                "FR Carte Vitale",
                \Mindee\Product\Fr\CarteVitale\CarteVitaleV1::class,
                true
            ),
            "fr-id-card" => new DocumentCommandConfig(
                "FR ID Card",
                \Mindee\Product\Fr\IdCard\IdCardV2::class,
                true
            ),
            "generated" => new DocumentCommandConfig(
                "Custom document type from docTI",
                \Mindee\Product\Generated\GeneratedV1::class,
                false,
                true
            ),
            "international-id" => new DocumentCommandConfig(
                "International Id",
                \Mindee\Product\InternationalId\InternationalIdV2::class,
                false,
                true
            ),
            "invoice" => new DocumentCommandConfig(
                "Invoice",
                \Mindee\Product\Invoice\InvoiceV4::class,
                true
            ),
            "invoice-splitter" => new DocumentCommandConfig(
                "Invoice Splitter",
                \Mindee\Product\InvoiceSplitter\InvoiceSplitterV1::class,
                false,
                true
            ),
            "multi-receipts" => new DocumentCommandConfig(
                "Multi-receipts detector",
                \Mindee\Product\MultiReceiptsDetector\MultiReceiptsDetectorV1::class,
                true
            ),
            "passport" => new DocumentCommandConfig(
                "Passport",
                \Mindee\Product\Passport\PassportV1::class,
                true
            ),
            "proof-of-address" => new DocumentCommandConfig(
                "Proof of Address",
                \Mindee\Product\ProofOfAddress\ProofOfAddressV1::class,
                true
            ),
            "receipt" => new DocumentCommandConfig(
                "Expense Receipt",
                \Mindee\Product\Receipt\ReceiptV5::class,
                true
            ),
            "resume" => new DocumentCommandConfig(
                "Resume",
                \Mindee\Product\Resume\ResumeV1::class,
                false,
                true
            ),
            "us-bank-check" => new DocumentCommandConfig(
                "US Bank Check",
                \Mindee\Product\Us\BankCheck\BankCheckV1::class,
                true
            ),
            "us-driver-license" => new DocumentCommandConfig(
                "US Driver License",
                \Mindee\Product\Us\DriverLicense\DriverLicenseV1::class,
                true
            ),
            "us-healthcare-card" => new DocumentCommandConfig(
                "US Healthcare Card",
                \Mindee\Product\Us\HealthcareCard\HealthcareCardV1::class,
                false,
                true
            ),
            "us-w9" => new DocumentCommandConfig(
                "US W9",
                \Mindee\Product\Us\W9\W9V1::class,
                true
            ),
        ];
    }
}
