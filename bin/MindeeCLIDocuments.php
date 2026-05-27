<?php

declare(strict_types=1);

namespace Mindee\Cli;

use Mindee\V1\Product\BarcodeReader\BarcodeReaderV1;
use Mindee\V1\Product\BillOfLading\BillOfLadingV1;
use Mindee\V1\Product\BusinessCard\BusinessCardV1;
use Mindee\V1\Product\Cropper\CropperV1;
use Mindee\V1\Product\DeliveryNote\DeliveryNoteV1;
use Mindee\V1\Product\DriverLicense\DriverLicenseV1;
use Mindee\V1\Product\FinancialDocument\FinancialDocumentV1;
use Mindee\V1\Product\Fr\BankAccountDetails\BankAccountDetailsV2;
use Mindee\V1\Product\Fr\CarteGrise\CarteGriseV1;
use Mindee\V1\Product\Fr\EnergyBill\EnergyBillV1;
use Mindee\V1\Product\Fr\HealthCard\HealthCardV1;
use Mindee\V1\Product\Fr\IdCard\IdCardV2;
use Mindee\V1\Product\Fr\Payslip\PayslipV3;
use Mindee\V1\Product\Generated\GeneratedV1;
use Mindee\V1\Product\Ind\IndianPassport\IndianPassportV1;
use Mindee\V1\Product\InternationalId\InternationalIdV2;
use Mindee\V1\Product\Invoice\InvoiceV4;
use Mindee\V1\Product\InvoiceSplitter\InvoiceSplitterV1;
use Mindee\V1\Product\MultiReceiptsDetector\MultiReceiptsDetectorV1;
use Mindee\V1\Product\NutritionFactsLabel\NutritionFactsLabelV1;
use Mindee\V1\Product\Passport\PassportV1;
use Mindee\V1\Product\Receipt\ReceiptV5;
use Mindee\V1\Product\Resume\ResumeV1;
use Mindee\V1\Product\Us\BankCheck\BankCheckV1;
use Mindee\V1\Product\Us\HealthcareCard\HealthcareCardV1;
use Mindee\V1\Product\Us\UsMail\UsMailV3;

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
                GeneratedV1::class,
                false,
                true
            ),
            "barcode-reader" => new DocumentCommandConfig(
                "Barcode Reader",
                BarcodeReaderV1::class,
                true,
                false
            ),
            "bill-of-lading" => new DocumentCommandConfig(
                "Bill of Lading",
                BillOfLadingV1::class,
                false,
                true
            ),
            "business-card" => new DocumentCommandConfig(
                "Business Card",
                BusinessCardV1::class,
                false,
                true
            ),
            "cropper" => new DocumentCommandConfig(
                "Cropper",
                CropperV1::class,
                true,
                false
            ),
            "delivery-note" => new DocumentCommandConfig(
                "Delivery note",
                DeliveryNoteV1::class,
                false,
                true
            ),
            "driver-license" => new DocumentCommandConfig(
                "Driver License",
                DriverLicenseV1::class,
                false,
                true
            ),
            "financial-document" => new DocumentCommandConfig(
                "Financial Document",
                FinancialDocumentV1::class,
                true,
                true
            ),
            "fr-bank-account-details" => new DocumentCommandConfig(
                "FR Bank Account Details",
                BankAccountDetailsV2::class,
                true,
                false
            ),
            "fr-carte-grise" => new DocumentCommandConfig(
                "FR Carte Grise",
                CarteGriseV1::class,
                true,
                false
            ),
            "fr-energy-bill" => new DocumentCommandConfig(
                "FR Energy Bill",
                EnergyBillV1::class,
                false,
                true
            ),
            "fr-health-card" => new DocumentCommandConfig(
                "FR Health Card",
                HealthCardV1::class,
                false,
                true
            ),
            "fr-carte-nationale-d-identite" => new DocumentCommandConfig(
                "FR Carte Nationale d'Identité",
                IdCardV2::class,
                true,
                false
            ),
            "fr-payslip" => new DocumentCommandConfig(
                "FR Payslip",
                PayslipV3::class,
                false,
                true
            ),
            "ind-passport-india" => new DocumentCommandConfig(
                "IND Passport - India",
                IndianPassportV1::class,
                false,
                true
            ),
            "international-id" => new DocumentCommandConfig(
                "International ID",
                InternationalIdV2::class,
                false,
                true
            ),
            "invoice" => new DocumentCommandConfig(
                "Invoice",
                InvoiceV4::class,
                true,
                true
            ),
            "invoice-splitter" => new DocumentCommandConfig(
                "Invoice Splitter",
                InvoiceSplitterV1::class,
                false,
                true
            ),
            "multi-receipts-detector" => new DocumentCommandConfig(
                "Multi Receipts Detector",
                MultiReceiptsDetectorV1::class,
                true,
                false
            ),
            "nutrition-facts-label" => new DocumentCommandConfig(
                "Nutrition Facts Label",
                NutritionFactsLabelV1::class,
                false,
                true
            ),
            "passport" => new DocumentCommandConfig(
                "Passport",
                PassportV1::class,
                true,
                false
            ),
            "receipt" => new DocumentCommandConfig(
                "Receipt",
                ReceiptV5::class,
                true,
                true
            ),
            "resume" => new DocumentCommandConfig(
                "Resume",
                ResumeV1::class,
                false,
                true
            ),
            "us-bank-check" => new DocumentCommandConfig(
                "US Bank Check",
                BankCheckV1::class,
                true,
                false
            ),
            "us-healthcare-card" => new DocumentCommandConfig(
                "US Healthcare Card",
                HealthcareCardV1::class,
                false,
                true
            ),
            "us-us-mail" => new DocumentCommandConfig(
                "US US Mail",
                UsMailV3::class,
                false,
                true
            ),
        ];
    }
}
