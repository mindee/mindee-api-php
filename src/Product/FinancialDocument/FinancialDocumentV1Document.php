<?php

namespace Mindee\Product\FinancialDocument;

use Mindee\Error\MindeeUnsetException;
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
 * Financial Document API version 1.9 document data.
 */
class FinancialDocumentV1Document extends Prediction
{
    /**
     * @var StringField The customer's address used for billing.
     */
    public StringField $billingAddress;
    /**
     * @var ClassificationField The purchase category among predefined classes.
     */
    public ClassificationField $category;
    /**
     * @var StringField The address of the customer.
     */
    public StringField $customerAddress;
    /**
     * @var CompanyRegistrationField[] List of company registrations associated to the customer.
     */
    public array $customerCompanyRegistrations;
    /**
     * @var StringField The customer account number or identifier from the supplier.
     */
    public StringField $customerId;
    /**
     * @var StringField The name of the customer.
     */
    public StringField $customerName;
    /**
     * @var DateField The date the purchase was made.
     */
    public DateField $date;
    /**
     * @var StringField The document number or identifier.
     */
    public StringField $documentNumber;
    /**
     * @var ClassificationField One of: 'INVOICE', 'CREDIT NOTE', 'CREDIT CARD RECEIPT', 'EXPENSE RECEIPT'.
     */
    public ClassificationField $documentType;
    /**
     * @var DateField The date on which the payment is due.
     */
    public DateField $dueDate;
    /**
     * @var StringField The invoice number or identifier only if document is an invoice.
     */
    public StringField $invoiceNumber;
    /**
     * @var FinancialDocumentV1LineItems List of line item details.
     */
    public FinancialDocumentV1LineItems $lineItems;
    /**
     * @var LocaleField The locale detected on the document.
     */
    public LocaleField $locale;
    /**
     * @var StringField The receipt number or identifier only if document is a receipt.
     */
    public StringField $receiptNumber;
    /**
     * @var StringField[] List of Reference numbers, including PO number.
     */
    public array $referenceNumbers;
    /**
     * @var StringField The customer's address used for shipping.
     */
    public StringField $shippingAddress;
    /**
     * @var ClassificationField The purchase subcategory among predefined classes for transport and food.
     */
    public ClassificationField $subcategory;
    /**
     * @var StringField The address of the supplier or merchant.
     */
    public StringField $supplierAddress;
    /**
     * @var CompanyRegistrationField[] List of company registrations associated to the supplier.
     */
    public array $supplierCompanyRegistrations;
    /**
     * @var StringField The email of the supplier or merchant.
     */
    public StringField $supplierEmail;
    /**
     * @var StringField The name of the supplier or merchant.
     */
    public StringField $supplierName;
    /**
     * @var PaymentDetailsField[] List of payment details associated to the supplier.
     */
    public array $supplierPaymentDetails;
    /**
     * @var StringField The phone number of the supplier or merchant.
     */
    public StringField $supplierPhoneNumber;
    /**
     * @var StringField The website URL of the supplier or merchant.
     */
    public StringField $supplierWebsite;
    /**
     * @var Taxes List of tax lines information.
     */
    public Taxes $taxes;
    /**
     * @var StringField The time the purchase was made.
     */
    public StringField $time;
    /**
     * @var AmountField The total amount of tip and gratuity
     */
    public AmountField $tip;
    /**
     * @var AmountField The total amount paid: includes taxes, tips, fees, and other charges.
     */
    public AmountField $totalAmount;
    /**
     * @var AmountField The net amount paid: does not include taxes, fees, and discounts.
     */
    public AmountField $totalNet;
    /**
     * @var AmountField The total amount of taxes.
     */
    public AmountField $totalTax;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["billing_address"])) {
            throw new MindeeUnsetException();
        }
        $this->billingAddress = new StringField(
            $rawPrediction["billing_address"],
            $pageId
        );
        if (!isset($rawPrediction["category"])) {
            throw new MindeeUnsetException();
        }
        $this->category = new ClassificationField(
            $rawPrediction["category"],
            $pageId
        );
        if (!isset($rawPrediction["customer_address"])) {
            throw new MindeeUnsetException();
        }
        $this->customerAddress = new StringField(
            $rawPrediction["customer_address"],
            $pageId
        );
        if (!isset($rawPrediction["customer_company_registrations"])) {
            throw new MindeeUnsetException();
        }
        $this->customerCompanyRegistrations = $rawPrediction["customer_company_registrations"] == null ? [] : array_map(
            fn ($prediction) => new CompanyRegistrationField($prediction, $pageId),
            $rawPrediction["customer_company_registrations"]
        );
        if (!isset($rawPrediction["customer_id"])) {
            throw new MindeeUnsetException();
        }
        $this->customerId = new StringField(
            $rawPrediction["customer_id"],
            $pageId
        );
        if (!isset($rawPrediction["customer_name"])) {
            throw new MindeeUnsetException();
        }
        $this->customerName = new StringField(
            $rawPrediction["customer_name"],
            $pageId
        );
        if (!isset($rawPrediction["date"])) {
            throw new MindeeUnsetException();
        }
        $this->date = new DateField(
            $rawPrediction["date"],
            $pageId
        );
        if (!isset($rawPrediction["document_number"])) {
            throw new MindeeUnsetException();
        }
        $this->documentNumber = new StringField(
            $rawPrediction["document_number"],
            $pageId
        );
        if (!isset($rawPrediction["document_type"])) {
            throw new MindeeUnsetException();
        }
        $this->documentType = new ClassificationField(
            $rawPrediction["document_type"],
            $pageId
        );
        if (!isset($rawPrediction["due_date"])) {
            throw new MindeeUnsetException();
        }
        $this->dueDate = new DateField(
            $rawPrediction["due_date"],
            $pageId
        );
        if (!isset($rawPrediction["invoice_number"])) {
            throw new MindeeUnsetException();
        }
        $this->invoiceNumber = new StringField(
            $rawPrediction["invoice_number"],
            $pageId
        );
        if (!isset($rawPrediction["line_items"])) {
            throw new MindeeUnsetException();
        }
        $this->lineItems = new FinancialDocumentV1LineItems(
            $rawPrediction["line_items"],
            $pageId
        );
        if (!isset($rawPrediction["locale"])) {
            throw new MindeeUnsetException();
        }
        $this->locale = new LocaleField(
            $rawPrediction["locale"],
            $pageId
        );
        if (!isset($rawPrediction["receipt_number"])) {
            throw new MindeeUnsetException();
        }
        $this->receiptNumber = new StringField(
            $rawPrediction["receipt_number"],
            $pageId
        );
        if (!isset($rawPrediction["reference_numbers"])) {
            throw new MindeeUnsetException();
        }
        $this->referenceNumbers = $rawPrediction["reference_numbers"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["reference_numbers"]
        );
        if (!isset($rawPrediction["shipping_address"])) {
            throw new MindeeUnsetException();
        }
        $this->shippingAddress = new StringField(
            $rawPrediction["shipping_address"],
            $pageId
        );
        if (!isset($rawPrediction["subcategory"])) {
            throw new MindeeUnsetException();
        }
        $this->subcategory = new ClassificationField(
            $rawPrediction["subcategory"],
            $pageId
        );
        if (!isset($rawPrediction["supplier_address"])) {
            throw new MindeeUnsetException();
        }
        $this->supplierAddress = new StringField(
            $rawPrediction["supplier_address"],
            $pageId
        );
        if (!isset($rawPrediction["supplier_company_registrations"])) {
            throw new MindeeUnsetException();
        }
        $this->supplierCompanyRegistrations = $rawPrediction["supplier_company_registrations"] == null ? [] : array_map(
            fn ($prediction) => new CompanyRegistrationField($prediction, $pageId),
            $rawPrediction["supplier_company_registrations"]
        );
        if (!isset($rawPrediction["supplier_email"])) {
            throw new MindeeUnsetException();
        }
        $this->supplierEmail = new StringField(
            $rawPrediction["supplier_email"],
            $pageId
        );
        if (!isset($rawPrediction["supplier_name"])) {
            throw new MindeeUnsetException();
        }
        $this->supplierName = new StringField(
            $rawPrediction["supplier_name"],
            $pageId
        );
        if (!isset($rawPrediction["supplier_payment_details"])) {
            throw new MindeeUnsetException();
        }
        $this->supplierPaymentDetails = $rawPrediction["supplier_payment_details"] == null ? [] : array_map(
            fn ($prediction) => new PaymentDetailsField($prediction, $pageId),
            $rawPrediction["supplier_payment_details"]
        );
        if (!isset($rawPrediction["supplier_phone_number"])) {
            throw new MindeeUnsetException();
        }
        $this->supplierPhoneNumber = new StringField(
            $rawPrediction["supplier_phone_number"],
            $pageId
        );
        if (!isset($rawPrediction["supplier_website"])) {
            throw new MindeeUnsetException();
        }
        $this->supplierWebsite = new StringField(
            $rawPrediction["supplier_website"],
            $pageId
        );
        if (!isset($rawPrediction["taxes"])) {
            throw new MindeeUnsetException();
        }
        $this->taxes = new Taxes(
            $rawPrediction["taxes"],
            $pageId
        );
        if (!isset($rawPrediction["time"])) {
            throw new MindeeUnsetException();
        }
        $this->time = new StringField(
            $rawPrediction["time"],
            $pageId
        );
        if (!isset($rawPrediction["tip"])) {
            throw new MindeeUnsetException();
        }
        $this->tip = new AmountField(
            $rawPrediction["tip"],
            $pageId
        );
        if (!isset($rawPrediction["total_amount"])) {
            throw new MindeeUnsetException();
        }
        $this->totalAmount = new AmountField(
            $rawPrediction["total_amount"],
            $pageId
        );
        if (!isset($rawPrediction["total_net"])) {
            throw new MindeeUnsetException();
        }
        $this->totalNet = new AmountField(
            $rawPrediction["total_net"],
            $pageId
        );
        if (!isset($rawPrediction["total_tax"])) {
            throw new MindeeUnsetException();
        }
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
        $lineItemsSummary = strval($this->lineItems);

        $outStr = ":Locale: $this->locale
:Invoice Number: $this->invoiceNumber
:Receipt Number: $this->receiptNumber
:Document Number: $this->documentNumber
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
:Supplier Website: $this->supplierWebsite
:Supplier Email: $this->supplierEmail
:Customer Company Registrations: $customerCompanyRegistrations
:Customer Address: $this->customerAddress
:Customer ID: $this->customerId
:Shipping Address: $this->shippingAddress
:Billing Address: $this->billingAddress
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
