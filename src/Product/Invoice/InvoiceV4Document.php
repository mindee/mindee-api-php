<?php

namespace Mindee\Product\Invoice;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Custom\ClassificationField;
use Mindee\Parsing\Standard\AmountField;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\LocaleField;
use Mindee\Parsing\Standard\StringField;
use Mindee\Parsing\Standard\Taxes;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1PageGroup;

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
     * @var \Mindee\Parsing\Standard\CompanyRegistrationField[] List of company registrations associated to the customer.
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
     * @param array $raw_prediction Raw prediction from HTTP response.
     * @param integer|null $page_id       Page number for multi pages PDF.
     */
    public function __construct(array $raw_prediction, ?int $page_id)
    {
        $this->invoicePageGroups = [];
        if (array_key_exists("invoice_page_groups", $raw_prediction)) {
            foreach ($raw_prediction['invoice_page_groups'] as $prediction) {
                $this->invoicePageGroups[] = new InvoiceSplitterV1PageGroup($prediction);
            }
        }
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $out_str = ":Invoice Page Groups:";
        foreach ($this->invoicePageGroups as $pageGroup) {
            $out_str .= "\n  $pageGroup";
        }
        return trim($out_str);
    }
}
