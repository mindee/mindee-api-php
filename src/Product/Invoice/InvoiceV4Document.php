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
     * @var InvoiceV4LineItems
     */
    public InvoiceV4LineItems $lineItems;
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
     * @param integer|null $pageId        Page number for multi pages document.
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
        $this->lineItems = new InvoiceV4LineItems($rawPrediction["line_items"], $pageId);
        $this->locale = new LocaleField($rawPrediction["locale"], $pageId);
        $this->referenceNumbers = array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["reference_numbers"]
        );
        $this->supplierAddress = new StringField($rawPrediction["supplier_address"], $pageId);
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
        return InvoiceV4LineItems::lineItemsSeparator($char);
    }

    /**
     * String representation for line items.
     *
     * @return string
     */
    private function lineItemsToStr(): string
    {
        return $this->lineItems->lineItemsToStr();
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $customerCompanyRegistrations = implode(
            "\n " . str_repeat(" ", 32),
            array_map(fn ($item) => strval($item), $this->customerCompanyRegistrations)
        );
        $referenceNumbers = implode(
            "\n " . str_repeat(" ", 19),
            array_map(fn ($item) => strval($item), $this->referenceNumbers)
        );
        $supplierCompanyRegistrations = implode(
            "\n " . str_repeat(" ", 32),
            array_map(fn ($item) => strval($item), $this->supplierCompanyRegistrations)
        );
        $supplierPaymentDetails = implode(
            "\n " . str_repeat(" ", 26),
            array_map(fn ($item) => strval($item), $this->supplierPaymentDetails)
        );
        $outStr = ":Locale: " . $this->locale . "\n";
        $outStr .= ":Invoice Number: " . $this->invoiceNumber . "\n";
        $outStr .= ":Reference Numbers: " . $referenceNumbers . "\n";
        $outStr .= ":Purchase Date: " . $this->date . "\n";
        $outStr .= ":Due Date: " . $this->dueDate . "\n";
        $outStr .= ":Total Net: " . $this->totalNet . "\n";
        $outStr .= ":Total Amount: " . $this->totalAmount . "\n";
        $outStr .= ":Taxes: " . $this->taxes . "\n";
        $outStr .= ":Supplier Payment Details: " . $supplierPaymentDetails . "\n";
        $outStr .= ":Supplier Name: " . $this->supplierName . "\n";
        $outStr .= ":Supplier Company Registrations: " . $supplierCompanyRegistrations . "\n";
        $outStr .= ":Supplier Address: " . $this->supplierAddress . "\n";
        $outStr .= ":Customer Name: " . $this->customerName . "\n";
        $outStr .= ":Customer Company Registrations: " . $customerCompanyRegistrations . "\n";
        $outStr .= ":Customer Address: " . $this->customerAddress . "\n";
        $outStr .= ":Document Type: " . $this->documentType . "\n";
        $outStr .= ":Line Items: " . $this->lineItemsToStr() . "\n";

        return SummaryHelper::cleanOutString($outStr);
    }
}
