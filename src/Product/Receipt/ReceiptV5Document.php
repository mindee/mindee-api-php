<?php

namespace Mindee\Product\Receipt;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\AmountField;
use Mindee\Parsing\Standard\ClassificationField;
use Mindee\Parsing\Standard\CompanyRegistrationField;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\LocaleField;
use Mindee\Parsing\Standard\StringField;
use Mindee\Parsing\Standard\Taxes;

/**
 * Document data for Receipt, API version 5.
 */
class ReceiptV5Document extends Prediction
{
    /**
    * @var ClassificationField The purchase category among predefined classes.
    */
    public ClassificationField $category;
    /**
    * @var DateField The date the purchase was made.
    */
    public DateField $date;
    /**
    * @var ClassificationField One of: 'CREDIT CARD RECEIPT', 'EXPENSE RECEIPT'.
    */
    public ClassificationField $documentType;
    /**
    * @var ReceiptV5LineItems List of line item details.
    */
    public ReceiptV5LineItems $lineItems;
    /**
    * @var LocaleField The locale detected on the document.
    */
    public LocaleField $locale;
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
    * @var StringField The name of the supplier or merchant.
    */
    public StringField $supplierName;
    /**
    * @var StringField The phone number of the supplier or merchant.
    */
    public StringField $supplierPhoneNumber;
    /**
    * @var Taxes List of tax lines information.
    */
    public Taxes $taxes;
    /**
    * @var StringField The time the purchase was made.
    */
    public StringField $time;
    /**
    * @var AmountField The total amount of tip and gratuity.
    */
    public AmountField $tip;
    /**
    * @var AmountField The total amount paid: includes taxes, discounts, fees, tips, and gratuity.
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
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->category = new ClassificationField(
            $rawPrediction["category"],
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
        $this->lineItems = new ReceiptV5LineItems(
            $rawPrediction["line_items"],
            $pageId
        );
        $this->locale = new LocaleField(
            $rawPrediction["locale"],
            $pageId
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
        $supplierCompanyRegistrations = implode(
            "\n                                 ",
            $this->supplierCompanyRegistrations
        );
        $lineItemsSummary = strval($this->lineItems);

        $outStr = ":Expense Locale: $this->locale
:Purchase Category: $this->category
:Purchase Subcategory: $this->subcategory
:Document Type: $this->documentType
:Purchase Date: $this->date
:Purchase Time: $this->time
:Total Amount: $this->totalAmount
:Total Net: $this->totalNet
:Total Tax: $this->totalTax
:Tip and Gratuity: $this->tip
:Taxes: $this->taxes
:Supplier Name: $this->supplierName
:Supplier Company Registrations: $supplierCompanyRegistrations
:Supplier Address: $this->supplierAddress
:Supplier Phone Number: $this->supplierPhoneNumber
:Line Items: $lineItemsSummary
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
