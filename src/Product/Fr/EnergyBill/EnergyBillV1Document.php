<?php

namespace Mindee\Product\Fr\EnergyBill;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\AmountField;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Energy Bill API version 1.2 document data.
 */
class EnergyBillV1Document extends Prediction
{
    /**
     * @var StringField The unique identifier associated with a specific contract.
     */
    public StringField $contractId;
    /**
     * @var StringField The unique identifier assigned to each electricity or gas consumption point. It specifies the
     * exact location where the energy is delivered.
     */
    public StringField $deliveryPoint;
    /**
     * @var DateField The date by which the payment for the energy invoice is due.
     */
    public DateField $dueDate;
    /**
     * @var EnergyBillV1EnergyConsumer The entity that consumes the energy.
     */
    public EnergyBillV1EnergyConsumer $energyConsumer;
    /**
     * @var EnergyBillV1EnergySupplier The company that supplies the energy.
     */
    public EnergyBillV1EnergySupplier $energySupplier;
    /**
     * @var EnergyBillV1EnergyUsages Details of energy consumption.
     */
    public EnergyBillV1EnergyUsages $energyUsage;
    /**
     * @var DateField The date when the energy invoice was issued.
     */
    public DateField $invoiceDate;
    /**
     * @var StringField The unique identifier of the energy invoice.
     */
    public StringField $invoiceNumber;
    /**
     * @var EnergyBillV1MeterDetail Information about the energy meter.
     */
    public EnergyBillV1MeterDetail $meterDetails;
    /**
     * @var EnergyBillV1Subscriptions The subscription details fee for the energy service.
     */
    public EnergyBillV1Subscriptions $subscription;
    /**
     * @var EnergyBillV1TaxesAndContributions Details of Taxes and Contributions.
     */
    public EnergyBillV1TaxesAndContributions $taxesAndContributions;
    /**
     * @var AmountField The total amount to be paid for the energy invoice.
     */
    public AmountField $totalAmount;
    /**
     * @var AmountField The total amount to be paid for the energy invoice before taxes.
     */
    public AmountField $totalBeforeTaxes;
    /**
     * @var AmountField Total of taxes applied to the invoice.
     */
    public AmountField $totalTaxes;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["contract_id"])) {
            throw new MindeeUnsetException();
        }
        $this->contractId = new StringField(
            $rawPrediction["contract_id"],
            $pageId
        );
        if (!isset($rawPrediction["delivery_point"])) {
            throw new MindeeUnsetException();
        }
        $this->deliveryPoint = new StringField(
            $rawPrediction["delivery_point"],
            $pageId
        );
        if (!isset($rawPrediction["due_date"])) {
            throw new MindeeUnsetException();
        }
        $this->dueDate = new DateField(
            $rawPrediction["due_date"],
            $pageId
        );
        if (!isset($rawPrediction["energy_consumer"])) {
            throw new MindeeUnsetException();
        }
        $this->energyConsumer = new EnergyBillV1EnergyConsumer(
            $rawPrediction["energy_consumer"],
            $pageId
        );
        if (!isset($rawPrediction["energy_supplier"])) {
            throw new MindeeUnsetException();
        }
        $this->energySupplier = new EnergyBillV1EnergySupplier(
            $rawPrediction["energy_supplier"],
            $pageId
        );
        if (!isset($rawPrediction["energy_usage"])) {
            throw new MindeeUnsetException();
        }
        $this->energyUsage = new EnergyBillV1EnergyUsages(
            $rawPrediction["energy_usage"],
            $pageId
        );
        if (!isset($rawPrediction["invoice_date"])) {
            throw new MindeeUnsetException();
        }
        $this->invoiceDate = new DateField(
            $rawPrediction["invoice_date"],
            $pageId
        );
        if (!isset($rawPrediction["invoice_number"])) {
            throw new MindeeUnsetException();
        }
        $this->invoiceNumber = new StringField(
            $rawPrediction["invoice_number"],
            $pageId
        );
        if (!isset($rawPrediction["meter_details"])) {
            throw new MindeeUnsetException();
        }
        $this->meterDetails = new EnergyBillV1MeterDetail(
            $rawPrediction["meter_details"],
            $pageId
        );
        if (!isset($rawPrediction["subscription"])) {
            throw new MindeeUnsetException();
        }
        $this->subscription = new EnergyBillV1Subscriptions(
            $rawPrediction["subscription"],
            $pageId
        );
        if (!isset($rawPrediction["taxes_and_contributions"])) {
            throw new MindeeUnsetException();
        }
        $this->taxesAndContributions = new EnergyBillV1TaxesAndContributions(
            $rawPrediction["taxes_and_contributions"],
            $pageId
        );
        if (!isset($rawPrediction["total_amount"])) {
            throw new MindeeUnsetException();
        }
        $this->totalAmount = new AmountField(
            $rawPrediction["total_amount"],
            $pageId
        );
        if (!isset($rawPrediction["total_before_taxes"])) {
            throw new MindeeUnsetException();
        }
        $this->totalBeforeTaxes = new AmountField(
            $rawPrediction["total_before_taxes"],
            $pageId
        );
        if (!isset($rawPrediction["total_taxes"])) {
            throw new MindeeUnsetException();
        }
        $this->totalTaxes = new AmountField(
            $rawPrediction["total_taxes"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $energySupplierToFieldList = $this->energySupplier != null ? $this->energySupplier->toFieldList() : "";
        $energyConsumerToFieldList = $this->energyConsumer != null ? $this->energyConsumer->toFieldList() : "";
        $subscriptionSummary = strval($this->subscription);
        $energyUsageSummary = strval($this->energyUsage);
        $taxesAndContributionsSummary = strval($this->taxesAndContributions);
        $meterDetailsToFieldList = $this->meterDetails != null ? $this->meterDetails->toFieldList() : "";

        $outStr = ":Invoice Number: $this->invoiceNumber
:Contract ID: $this->contractId
:Delivery Point: $this->deliveryPoint
:Invoice Date: $this->invoiceDate
:Due Date: $this->dueDate
:Total Before Taxes: $this->totalBeforeTaxes
:Total Taxes: $this->totalTaxes
:Total Amount: $this->totalAmount
:Energy Supplier: $energySupplierToFieldList
:Energy Consumer: $energyConsumerToFieldList
:Subscription: $subscriptionSummary
:Energy Usage: $energyUsageSummary
:Taxes and Contributions: $taxesAndContributionsSummary
:Meter Details: $meterDetailsToFieldList
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
