<?php

namespace Mindee\Product\Invoice;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\ClassificationField;
use Mindee\Parsing\Standard\AmountField;
use Mindee\Parsing\Standard\CompanyRegistrationField;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\LocaleField;
use Mindee\Parsing\Standard\PaymentDetailsField;
use Mindee\Parsing\Standard\StringField;
use Mindee\Parsing\Standard\Taxes;

/**
 * Document data for Invoice, API version 4.
 */
class InvoiceV4Document extends Prediction
{
    /**
     * @var StringField The address of the customer.
     */
    public StringField $customerAddress;
    /**
     * @var \Mindee\Parsing\Standard\CompanyRegistrationField[] List of company registrations associated to the
     * customer.
     */
    public array $customerCompanyRegistrations;
    /**
     * @var StringField
     */
    public StringField $customerName;
    /**
     * @var DateField
     */
    public DateField $date;
    /**
     * @var ClassificationField
     */
    public ClassificationField $documentType;
    /**
     * @var DateField
     */
    public DateField $dueDate;
    /**
     * @var StringField
     */
    public StringField $invoiceNumber;
    /**
     * @var InvoiceV4LineItems[]
     */
    public array $lineItems;
    /**
     * @var LocaleField
     */
    public LocaleField $locale;
    /**
     * @var StringField[]
     */
    public array $referenceNumbers;
    /**
     * @var StringField
     */
    public StringField $supplierAddress;
    /**
     * @var \Mindee\Parsing\Standard\CompanyRegistrationField[]
     */
    public array $supplierCompanyRegistrations;
    /**
     * @var StringField
     */
    public StringField $supplierName;
    /**
     * @var \Mindee\Parsing\Standard\PaymentDetailsField[]
     */
    public array $supplierPaymentDetails;
    /**
     * @var \Mindee\Parsing\Standard\Taxes
     */
    public Taxes $taxes;
    /**
     * @var AmountField
     */
    public AmountField $totalAmount;
    /**
     * @var AmountField
     */
    public AmountField $totalNet;

    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages PDF.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->customerAddress = new StringField($rawPrediction["customer_address"], $pageId);
        $this->customerCompanyRegistrations = array_map(
            fn ($prediction) => new CompanyRegistrationField($prediction, $pageId),
            $rawPrediction["customer_company_registrations"]
        );
        $this->customerName = new StringField($rawPrediction["customer_name"], $pageId);
        $this->date = new DateField($rawPrediction["date"], $pageId);
        $this->documentType = new ClassificationField($rawPrediction["document_type"], $pageId);
        $this->dueDate = new DateField($rawPrediction["due_date"], $pageId);
        $this->invoiceNumber = new StringField($rawPrediction["invoice_number"], $pageId);
        $this->lineItems = array_map(
            fn ($prediction) => new InvoiceV4LineItems($prediction, $pageId),
            $rawPrediction["line_items"]
        );
        $this->locale = new LocaleField($rawPrediction["locale"], $pageId);
        $this->supplierCompanyRegistrations = array_map(
            fn ($prediction) => new CompanyRegistrationField($prediction, $pageId),
            $rawPrediction["supplier_company_registrations"]
        );
        $this->supplierName = new StringField($rawPrediction["supplier_name"], $pageId);
        $this->supplierPaymentDetails = array_map(
            fn ($prediction) => new PaymentDetailsField($prediction, $pageId),
            $rawPrediction["supplier_payment_details"]
        );
        $this->taxes = new Taxes($rawPrediction['taxes'], $pageId);
        $this->totalAmount = new AmountField($rawPrediction['total_amount'], $pageId);
        $this->totalNet = new AmountField($rawPrediction['total_net'], $pageId);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    private static function lineItemsSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 38);
        $outStr .= "+" . str_repeat($char, 14);
        $outStr .= "+" . str_repeat($char, 10);
        $outStr .= "+" . str_repeat($char, 12);
        $outStr .= "+" . str_repeat($char, 14);
        $outStr .= "+" . str_repeat($char, 14);
        $outStr .= "+" . str_repeat($char, 12);
        return $outStr . "+";
    }

    /**
     * String representation for line items.
     *
     * @return string
     */
    private function lineItemsToStr(): string
    {
        if (!$this->lineItems || count($this->lineItems) == 0) {
            return "";
        }
        $lines = "\n" . self::lineItemsSeparator('-') . implode(
            "\n  ",
            array_map(fn ($item) => $item->toTableLine(), $this->lineItems)
        );
        $outStr = "\n" . self::lineItemsSeparator('-') . "\n ";
        $outStr .= " | Description                         ";
        $outStr .= " | Product code";
        $outStr .= " | Quantity";
        $outStr .= " | Tax Amount";
        $outStr .= " | Tax Rate (%)";
        $outStr .= " | Total Amount";
        $outStr .= " | Unit Price";
        $outStr .= " |\n" . self::lineItemsSeparator("=");
        $outStr .= "\n  $lines";
        $outStr .= " |\n" . self::lineItemsSeparator("-");
        return $outStr;
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $customerCompanyRegistrations = "\n " . implode(
            str_repeat(" ", 32),
            array_map(fn ($item) => strval($item), $this->customerCompanyRegistrations)
        );
        $referenceNumbers = "\n " . implode(
            str_repeat(" ", 19),
            array_map(fn ($item) => strval($item), $this->referenceNumbers)
        );
        $supplierCompanyRegistrations = "\n " . implode(
            str_repeat(" ", 32),
            array_map(fn ($item) => strval($item), $this->supplierCompanyRegistrations)
        );
        $supplierPaymentDetails = "\n " . implode(
            str_repeat(" ", 26),
            array_map(fn ($item) => strval($item), $this->supplierPaymentDetails)
        );
        $outStr = ":Locale: " . $this->locale . "\n";
        $outStr .= ":InvoiceNumber: " . $this->invoiceNumber . "\n";
        $outStr .= ":ReferenceNumbers: " . $referenceNumbers . "\n";
        $outStr .= ":PurchaseDate: " . $this->date . "\n";
        $outStr .= ":DueDate: " . $this->dueDate . "\n";
        $outStr .= ":TotalNet: " . $this->totalNet . "\n";
        $outStr .= ":TotalAmount: " . $this->totalAmount . "\n";
        $outStr .= ":Taxes: " . $this->taxes . "\n";
        $outStr .= ":SupplierPaymentDetails: " . $supplierPaymentDetails . "\n";
        $outStr .= ":SupplierName: " . $this->supplierName . "\n";
        $outStr .= ":SupplierCompanyRegistrations: " . $supplierCompanyRegistrations . "\n";
        $outStr .= ":SupplierAddress: " . $this->supplierAddress . "\n";
        $outStr .= ":CustomerName: " . $this->customerName . "\n";
        $outStr .= ":CustomerCompanyRegistrations: " . $customerCompanyRegistrations . "\n";
        $outStr .= ":CustomerAddress: " . $this->customerAddress . "\n";
        $outStr .= ":DocumentType: " . $this->documentType . "\n";
        $outStr .= ":Line Items: " . $this->lineItemsToStr() . "\n";

        return SummaryHelper::cleanOutString($outStr);
    }
}
