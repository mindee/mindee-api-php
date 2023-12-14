<?php

namespace Mindee\Product\Invoice;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\AmountField;
use Mindee\Parsing\Standard\ClassificationField;
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
    * @var CompanyRegistrationField[] List of company registrations associated to the customer.
    */
    public array $customerCompanyRegistrations;
    /**
    * @var StringField The name of the customer or client.
    */
    public StringField $customerName;
    /**
    * @var DateField The date the purchase was made.
    */
    public DateField $date;
    /**
    * @var ClassificationField One of: 'INVOICE', 'CREDIT NOTE'.
    */
    public ClassificationField $documentType;
    /**
    * @var DateField The date on which the payment is due.
    */
    public DateField $dueDate;
    /**
    * @var StringField The invoice number or identifier.
    */
    public StringField $invoiceNumber;
    /**
    * @var InvoiceV4LineItems List of line item details.
    */
    public InvoiceV4LineItems $lineItems;
    /**
    * @var LocaleField The locale detected on the document.
    */
    public LocaleField $locale;
    /**
    * @var StringField[] List of Reference numbers, including PO number.
    */
    public array $referenceNumbers;
    /**
    * @var StringField The address of the supplier or merchant.
    */
    public StringField $supplierAddress;
    /**
    * @var CompanyRegistrationField[] List of company registrations associated to the supplier.
    */
    public array $supplierCompanyRegistrations;
    /**
    * @var StringField The name of the supplier or merchant.
    */
    public StringField $supplierName;
    /**
    * @var PaymentDetailsField[] List of payment details associated to the supplier.
    */
    public array $supplierPaymentDetails;
    /**
    * @var Taxes List of tax line details.
    */
    public Taxes $taxes;
    /**
    * @var AmountField The total amount paid: includes taxes, tips, fees, and other charges.
    */
    public AmountField $totalAmount;
    /**
    * @var AmountField The net amount paid: does not include taxes, fees, and discounts.
    */
    public AmountField $totalNet;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->customerAddress = new StringField(
            $rawPrediction["customer_address"],
            $pageId
        );
        $this->customerCompanyRegistrations = $rawPrediction["customer_company_registrations"] == null ? [] : array_map(
            fn ($prediction) => new CompanyRegistrationField($prediction, $pageId),
            $rawPrediction["customer_company_registrations"]
        );
        $this->customerName = new StringField(
            $rawPrediction["customer_name"],
            $pageId
        );
        $this->date = new DateField(
            $rawPrediction["date"],
            $pageId
        );
        $this->documentType = new ClassificationField(
            $rawPrediction["document_type"],
            $pageId
        );
        $this->dueDate = new DateField(
            $rawPrediction["due_date"],
            $pageId
        );
        $this->invoiceNumber = new StringField(
            $rawPrediction["invoice_number"],
            $pageId
        );
        $this->lineItems = new InvoiceV4LineItems(
            $rawPrediction["line_items"],
            $pageId
        );
        $this->locale = new LocaleField(
            $rawPrediction["locale"],
            $pageId
        );
        $this->referenceNumbers = $rawPrediction["reference_numbers"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["reference_numbers"]
        );
        $this->supplierAddress = new StringField(
            $rawPrediction["supplier_address"],
            $pageId
        );
        $this->supplierCompanyRegistrations = $rawPrediction["supplier_company_registrations"] == null ? [] : array_map(
            fn ($prediction) => new CompanyRegistrationField($prediction, $pageId),
            $rawPrediction["supplier_company_registrations"]
        );
        $this->supplierName = new StringField(
            $rawPrediction["supplier_name"],
            $pageId
        );
        $this->supplierPaymentDetails = $rawPrediction["supplier_payment_details"] == null ? [] : array_map(
            fn ($prediction) => new PaymentDetailsField($prediction, $pageId),
            $rawPrediction["supplier_payment_details"]
        );
        $this->taxes = new Taxes(
            $rawPrediction["taxes"],
            $pageId
        );
        $this->totalAmount = new AmountField(
            $rawPrediction["total_amount"],
            $pageId
        );
        $this->totalNet = new AmountField(
            $rawPrediction["total_net"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $referenceNumbers = implode(
            "\n                    ",
            $this->referenceNumbers
        );
        $supplierPaymentDetails = implode(
            "\n                           ",
            $this->supplierPaymentDetails
        );
        $supplierCompanyRegistrations = implode(
            "\n                                 ",
            $this->supplierCompanyRegistrations
        );
        $customerCompanyRegistrations = implode(
            "\n                                 ",
            $this->customerCompanyRegistrations
        );
        $lineItemsSummary = strval($this->lineItems);

        $outStr = ":Locale: $this->locale
:Invoice Number: $this->invoiceNumber
:Reference Numbers: $referenceNumbers
:Purchase Date: $this->date
:Due Date: $this->dueDate
:Total Net: $this->totalNet
:Total Amount: $this->totalAmount
:Taxes: $this->taxes
:Supplier Payment Details: $supplierPaymentDetails
:Supplier Name: $this->supplierName
:Supplier Company Registrations: $supplierCompanyRegistrations
:Supplier Address: $this->supplierAddress
:Customer Name: $this->customerName
:Customer Company Registrations: $customerCompanyRegistrations
:Customer Address: $this->customerAddress
:Document Type: $this->documentType
:Line Items: $lineItemsSummary
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
