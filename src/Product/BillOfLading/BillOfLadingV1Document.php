<?php

namespace Mindee\Product\BillOfLading;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Bill of Lading API version 1.1 document data.
 */
class BillOfLadingV1Document extends Prediction
{
    /**
     * @var StringField A unique identifier assigned to a Bill of Lading document.
     */
    public StringField $billOfLadingNumber;
    /**
     * @var BillOfLadingV1Carrier The shipping company responsible for transporting the goods.
     */
    public BillOfLadingV1Carrier $carrier;
    /**
     * @var BillOfLadingV1CarrierItems The goods being shipped.
     */
    public BillOfLadingV1CarrierItems $carrierItems;
    /**
     * @var BillOfLadingV1Consignee The party to whom the goods are being shipped.
     */
    public BillOfLadingV1Consignee $consignee;
    /**
     * @var DateField The date when the bill of lading is issued.
     */
    public DateField $dateOfIssue;
    /**
     * @var DateField The date when the vessel departs from the port of loading.
     */
    public DateField $departureDate;
    /**
     * @var BillOfLadingV1NotifyParty The party to be notified of the arrival of the goods.
     */
    public BillOfLadingV1NotifyParty $notifyParty;
    /**
     * @var StringField The place where the goods are to be delivered.
     */
    public StringField $placeOfDelivery;
    /**
     * @var StringField The port where the goods are unloaded from the vessel.
     */
    public StringField $portOfDischarge;
    /**
     * @var StringField The port where the goods are loaded onto the vessel.
     */
    public StringField $portOfLoading;
    /**
     * @var BillOfLadingV1Shipper The party responsible for shipping the goods.
     */
    public BillOfLadingV1Shipper $shipper;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["bill_of_lading_number"])) {
            throw new MindeeUnsetException();
        }
        $this->billOfLadingNumber = new StringField(
            $rawPrediction["bill_of_lading_number"],
            $pageId
        );
        if (!isset($rawPrediction["carrier"])) {
            throw new MindeeUnsetException();
        }
        $this->carrier = new BillOfLadingV1Carrier(
            $rawPrediction["carrier"],
            $pageId
        );
        if (!isset($rawPrediction["carrier_items"])) {
            throw new MindeeUnsetException();
        }
        $this->carrierItems = new BillOfLadingV1CarrierItems(
            $rawPrediction["carrier_items"],
            $pageId
        );
        if (!isset($rawPrediction["consignee"])) {
            throw new MindeeUnsetException();
        }
        $this->consignee = new BillOfLadingV1Consignee(
            $rawPrediction["consignee"],
            $pageId
        );
        if (!isset($rawPrediction["date_of_issue"])) {
            throw new MindeeUnsetException();
        }
        $this->dateOfIssue = new DateField(
            $rawPrediction["date_of_issue"],
            $pageId
        );
        if (!isset($rawPrediction["departure_date"])) {
            throw new MindeeUnsetException();
        }
        $this->departureDate = new DateField(
            $rawPrediction["departure_date"],
            $pageId
        );
        if (!isset($rawPrediction["notify_party"])) {
            throw new MindeeUnsetException();
        }
        $this->notifyParty = new BillOfLadingV1NotifyParty(
            $rawPrediction["notify_party"],
            $pageId
        );
        if (!isset($rawPrediction["place_of_delivery"])) {
            throw new MindeeUnsetException();
        }
        $this->placeOfDelivery = new StringField(
            $rawPrediction["place_of_delivery"],
            $pageId
        );
        if (!isset($rawPrediction["port_of_discharge"])) {
            throw new MindeeUnsetException();
        }
        $this->portOfDischarge = new StringField(
            $rawPrediction["port_of_discharge"],
            $pageId
        );
        if (!isset($rawPrediction["port_of_loading"])) {
            throw new MindeeUnsetException();
        }
        $this->portOfLoading = new StringField(
            $rawPrediction["port_of_loading"],
            $pageId
        );
        if (!isset($rawPrediction["shipper"])) {
            throw new MindeeUnsetException();
        }
        $this->shipper = new BillOfLadingV1Shipper(
            $rawPrediction["shipper"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $shipperToFieldList = $this->shipper != null ? $this->shipper->toFieldList() : "";
        $consigneeToFieldList = $this->consignee != null ? $this->consignee->toFieldList() : "";
        $notifyPartyToFieldList = $this->notifyParty != null ? $this->notifyParty->toFieldList() : "";
        $carrierToFieldList = $this->carrier != null ? $this->carrier->toFieldList() : "";
        $carrierItemsSummary = strval($this->carrierItems);

        $outStr = ":Bill of Lading Number: $this->billOfLadingNumber
:Shipper: $shipperToFieldList
:Consignee: $consigneeToFieldList
:Notify Party: $notifyPartyToFieldList
:Carrier: $carrierToFieldList
:Items: $carrierItemsSummary
:Port of Loading: $this->portOfLoading
:Port of Discharge: $this->portOfDischarge
:Place of Delivery: $this->placeOfDelivery
:Date of issue: $this->dateOfIssue
:Departure Date: $this->departureDate
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
