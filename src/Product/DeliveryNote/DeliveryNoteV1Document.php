<?php

namespace Mindee\Product\DeliveryNote;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\AmountField;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Delivery note API version 1.2 document data.
 */
class DeliveryNoteV1Document extends Prediction
{
    /**
     * @var StringField The address of the customer receiving the goods.
     */
    public StringField $customerAddress;
    /**
     * @var StringField The name of the customer receiving the goods.
     */
    public StringField $customerName;
    /**
     * @var DateField The date on which the delivery is scheduled to arrive.
     */
    public DateField $deliveryDate;
    /**
     * @var StringField A unique identifier for the delivery note.
     */
    public StringField $deliveryNumber;
    /**
     * @var StringField The address of the supplier providing the goods.
     */
    public StringField $supplierAddress;
    /**
     * @var StringField The name of the supplier providing the goods.
     */
    public StringField $supplierName;
    /**
     * @var AmountField The total monetary value of the goods being delivered.
     */
    public AmountField $totalAmount;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["customer_address"])) {
            throw new MindeeUnsetException();
        }
        $this->customerAddress = new StringField(
            $rawPrediction["customer_address"],
            $pageId
        );
        if (!isset($rawPrediction["customer_name"])) {
            throw new MindeeUnsetException();
        }
        $this->customerName = new StringField(
            $rawPrediction["customer_name"],
            $pageId
        );
        if (!isset($rawPrediction["delivery_date"])) {
            throw new MindeeUnsetException();
        }
        $this->deliveryDate = new DateField(
            $rawPrediction["delivery_date"],
            $pageId
        );
        if (!isset($rawPrediction["delivery_number"])) {
            throw new MindeeUnsetException();
        }
        $this->deliveryNumber = new StringField(
            $rawPrediction["delivery_number"],
            $pageId
        );
        if (!isset($rawPrediction["supplier_address"])) {
            throw new MindeeUnsetException();
        }
        $this->supplierAddress = new StringField(
            $rawPrediction["supplier_address"],
            $pageId
        );
        if (!isset($rawPrediction["supplier_name"])) {
            throw new MindeeUnsetException();
        }
        $this->supplierName = new StringField(
            $rawPrediction["supplier_name"],
            $pageId
        );
        if (!isset($rawPrediction["total_amount"])) {
            throw new MindeeUnsetException();
        }
        $this->totalAmount = new AmountField(
            $rawPrediction["total_amount"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {

        $outStr = ":Delivery Date: $this->deliveryDate
:Delivery Number: $this->deliveryNumber
:Supplier Name: $this->supplierName
:Supplier Address: $this->supplierAddress
:Customer Name: $this->customerName
:Customer Address: $this->customerAddress
:Total Amount: $this->totalAmount
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
