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
            "generated" => new DocumentCommandConfig(
                "Custom document type from docTI",
                \Mindee\Product\Generated\GeneratedV1::class,
                false,
                true
            ),
            "barcode-reader" => new DocumentCommandConfig(
                "Barcode Reader",
                \Mindee\Product\BarcodeReader\BarcodeReaderV1::class,
                true,
                false
            ),
            "bill-of-lading" => new DocumentCommandConfig(
                "Bill of Lading",
                \Mindee\Product\BillOfLading\BillOfLadingV1::class,
                false,
                true
            ),
            "business-card" => new DocumentCommandConfig(
                "Business Card",
                \Mindee\Product\BusinessCard\BusinessCardV1::class,
                false,
                true
            ),
            "cropper" => new DocumentCommandConfig(
                "Cropper",
                \Mindee\Product\Cropper\CropperV1::class,
                true,
                false
            ),
            "delivery-note" => new DocumentCommandConfig(
                "Delivery note",
                \Mindee\Product\DeliveryNote\DeliveryNoteV1::class,
                false,
                true
            ),
            "driver-license" => new DocumentCommandConfig(
                "Driver License",
                \Mindee\Product\DriverLicense\DriverLicenseV1::class,
                false,
                true
            ),
            "financial-document" => new DocumentCommandConfig(
                "Financial Document",
                \Mindee\Product\FinancialDocument\FinancialDocumentV1::class,
                true,
                true
            ),
            "fr-bank-account-details" => new DocumentCommandConfig(
                "FR Bank Account Details",
                \Mindee\Product\Fr\BankAccountDetails\BankAccountDetailsV2::class,
                true,
                false
            ),
            "fr-carte-grise" => new DocumentCommandConfig(
                "FR Carte Grise",
                \Mindee\Product\Fr\CarteGrise\CarteGriseV1::class,
                true,
                false
            ),
            "fr-energy-bill" => new DocumentCommandConfig(
                "FR Energy Bill",
                \Mindee\Product\Fr\EnergyBill\EnergyBillV1::class,
                false,
                true
            ),
            "fr-health-card" => new DocumentCommandConfig(
                "FR Health Card",
                \Mindee\Product\Fr\HealthCard\HealthCardV1::class,
                false,
                true
            ),
            "fr-carte-nationale-d-identite" => new DocumentCommandConfig(
                "FR Carte Nationale d'Identité",
                \Mindee\Product\Fr\IdCard\IdCardV2::class,
                true,
                false
            ),
            "fr-payslip" => new DocumentCommandConfig(
                "FR Payslip",
                \Mindee\Product\Fr\Payslip\PayslipV3::class,
                false,
                true
            ),
            "ind-passport-india" => new DocumentCommandConfig(
                "IND Passport - India",
                \Mindee\Product\Ind\IndianPassport\IndianPassportV1::class,
                false,
                true
            ),
            "international-id" => new DocumentCommandConfig(
                "International ID",
                \Mindee\Product\InternationalId\InternationalIdV2::class,
                false,
                true
            ),
            "invoice" => new DocumentCommandConfig(
                "Invoice",
                \Mindee\Product\Invoice\InvoiceV4::class,
                true,
                true
            ),
            "invoice-splitter" => new DocumentCommandConfig(
                "Invoice Splitter",
                \Mindee\Product\InvoiceSplitter\InvoiceSplitterV1::class,
                false,
                true
            ),
            "multi-receipts-detector" => new DocumentCommandConfig(
                "Multi Receipts Detector",
                \Mindee\Product\MultiReceiptsDetector\MultiReceiptsDetectorV1::class,
                true,
                false
            ),
            "nutrition-facts-label" => new DocumentCommandConfig(
                "Nutrition Facts Label",
                \Mindee\Product\NutritionFactsLabel\NutritionFactsLabelV1::class,
                false,
                true
            ),
            "passport" => new DocumentCommandConfig(
                "Passport",
                \Mindee\Product\Passport\PassportV1::class,
                true,
                false
            ),
            "receipt" => new DocumentCommandConfig(
                "Receipt",
                \Mindee\Product\Receipt\ReceiptV5::class,
                true,
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
                true,
                false
            ),
            "us-healthcare-card" => new DocumentCommandConfig(
                "US Healthcare Card",
                \Mindee\Product\Us\HealthcareCard\HealthcareCardV1::class,
                false,
                true
            ),
            "us-us-mail" => new DocumentCommandConfig(
                "US US Mail",
                \Mindee\Product\Us\UsMail\UsMailV3::class,
                false,
                true
            ),
        ];
    }
}
