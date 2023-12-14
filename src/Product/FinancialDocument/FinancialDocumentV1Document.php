<?php

namespace Mindee\Product\FinancialDocument;

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
 * Document data for Financial Document, API version 1.
 */
class FinancialDocumentV1Document extends Prediction
{
    /**
    * @var ClassificationField|null The purchase category among predefined classes.
    */
    public ?ClassificationField $category;
    /**
    * @var StringField|null The address of the customer.
    */
    public ?StringField $customerAddress;
    /**
    * @var CompanyRegistrationField[]|null List of company registrations associated to the customer.
    */
    public ?array $customerCompanyRegistrations;
    /**
    * @var StringField|null The name of the customer.
    */
    public ?StringField $customerName;
    /**
    * @var DateField|null The date the purchase was made.
    */
    public ?DateField $date;
    /**
    * @var ClassificationField|null One of: 'INVOICE', 'CREDIT NOTE', 'CREDIT CARD RECEIPT', 'EXPENSE RECEIPT'.
    */
    public ?ClassificationField $documentType;
    /**
    * @var DateField|null The date on which the payment is due.
    */
    public ?DateField $dueDate;
    /**
    * @var StringField|null The invoice number or identifier.
    */
    public ?StringField $invoiceNumber;
    /**
    * @var FinancialDocumentV1LineItems List of line item details.
    */
    public FinancialDocumentV1LineItems $lineItems;
    /**
    * @var LocaleField|null The locale detected on the document.
    */
    public ?LocaleField $locale;
    /**
    * @var StringField[]|null List of Reference numbers, including PO number.
    */
    public ?array $referenceNumbers;
    /**
    * @var ClassificationField|null The purchase subcategory among predefined classes for transport and food.
    */
    public ?ClassificationField $subcategory;
    /**
    * @var StringField|null The address of the supplier or merchant.
    */
    public ?StringField $supplierAddress;
    /**
    * @var CompanyRegistrationField[]|null List of company registrations associated to the supplier.
    */
    public ?array $supplierCompanyRegistrations;
    /**
    * @var StringField|null The name of the supplier or merchant.
    */
    public ?StringField $supplierName;
    /**
    * @var PaymentDetailsField[]|null List of payment details associated to the supplier.
    */
    public ?array $supplierPaymentDetails;
    /**
    * @var StringField|null The phone number of the supplier or merchant.
    */
    public ?StringField $supplierPhoneNumber;
    /**
    * @var Taxes List of tax lines information.
    */
    public Taxes $taxes;
    /**
    * @var StringField|null The time the purchase was made.
    */
    public ?StringField $time;
    /**
    * @var AmountField|null The total amount of tip and gratuity
    */
    public ?AmountField $tip;
    /**
    * @var AmountField|null The total amount paid: includes taxes, tips, fees, and other charges.
    */
    public ?AmountField $totalAmount;
    /**
    * @var AmountField|null The net amount paid: does not include taxes, fees, and discounts.
    */
    public ?AmountField $totalNet;
    /**
    * @var AmountField|null The total amount of taxes.
    */
    public ?AmountField $totalTax;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->category = new ClassificationField(
            $rawPrediction["category"],
            $pageId
        );
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
        $this->lineItems = new FinancialDocumentV1LineItems(
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
        $this->subcategory = new ClassificationField(
            $rawPrediction["subcategory"],
            $pageId
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
        $this->supplierPhoneNumber = new StringField(
            $rawPrediction["supplier_phone_number"],
            $pageId
        );
        $this->taxes = new Taxes(
            $rawPrediction["taxes"],
            $pageId
        );
        $this->time = new StringField(
            $rawPrediction["time"],
            $pageId
        );
        $this->tip = new AmountField(
            $rawPrediction["tip"],
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
        $this->totalTax = new AmountField(
            $rawPrediction["total_tax"],
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
        $lineItemsSummary = $this->lineItems->lineItemsToStr();

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
:Supplier Phone Number: $this->supplierPhoneNumber
:Customer Name: $this->customerName
:Customer Company Registrations: $customerCompanyRegistrations
:Customer Address: $this->customerAddress
:Document Type: $this->documentType
:Purchase Subcategory: $this->subcategory
:Purchase Category: $this->category
:Total Tax: $this->totalTax
:Tip and Gratuity: $this->tip
:Purchase Time: $this->time
:Line Items: $lineItemsSummary
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
