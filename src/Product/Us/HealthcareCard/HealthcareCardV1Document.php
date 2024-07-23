<?php

namespace Mindee\Product\Us\HealthcareCard;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Healthcare Card API version 1.0 document data.
 */
class HealthcareCardV1Document extends Prediction
{
    /**
     * @var StringField The name of the company that provides the healthcare plan.
     */
    public StringField $companyName;
    /**
     * @var HealthcareCardV1Copays Is a fixed amount for a covered service.
     */
    public HealthcareCardV1Copays $copays;
    /**
     * @var StringField[] The list of dependents covered by the healthcare plan.
     */
    public array $dependents;
    /**
     * @var DateField The date when the member enrolled in the healthcare plan.
     */
    public DateField $enrollmentDate;
    /**
     * @var StringField The group number associated with the healthcare plan.
     */
    public StringField $groupNumber;
    /**
     * @var StringField The organization that issued the healthcare plan.
     */
    public StringField $issuer80840;
    /**
     * @var StringField The unique identifier for the member in the healthcare system.
     */
    public StringField $memberId;
    /**
     * @var StringField The name of the member covered by the healthcare plan.
     */
    public StringField $memberName;
    /**
     * @var StringField The unique identifier for the payer in the healthcare system.
     */
    public StringField $payerId;
    /**
     * @var StringField The BIN number for prescription drug coverage.
     */
    public StringField $rxBin;
    /**
     * @var StringField The group number for prescription drug coverage.
     */
    public StringField $rxGrp;
    /**
     * @var StringField The PCN number for prescription drug coverage.
     */
    public StringField $rxPcn;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["company_name"])) {
            throw new MindeeUnsetException();
        }
        $this->companyName = new StringField(
            $rawPrediction["company_name"],
            $pageId
        );
        if (!isset($rawPrediction["copays"])) {
            throw new MindeeUnsetException();
        }
        $this->copays = new HealthcareCardV1Copays(
            $rawPrediction["copays"],
            $pageId
        );
        if (!isset($rawPrediction["dependents"])) {
            throw new MindeeUnsetException();
        }
        $this->dependents = $rawPrediction["dependents"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["dependents"]
        );
        if (!isset($rawPrediction["enrollment_date"])) {
            throw new MindeeUnsetException();
        }
        $this->enrollmentDate = new DateField(
            $rawPrediction["enrollment_date"],
            $pageId
        );
        if (!isset($rawPrediction["group_number"])) {
            throw new MindeeUnsetException();
        }
        $this->groupNumber = new StringField(
            $rawPrediction["group_number"],
            $pageId
        );
        if (!isset($rawPrediction["issuer_80840"])) {
            throw new MindeeUnsetException();
        }
        $this->issuer80840 = new StringField(
            $rawPrediction["issuer_80840"],
            $pageId
        );
        if (!isset($rawPrediction["member_id"])) {
            throw new MindeeUnsetException();
        }
        $this->memberId = new StringField(
            $rawPrediction["member_id"],
            $pageId
        );
        if (!isset($rawPrediction["member_name"])) {
            throw new MindeeUnsetException();
        }
        $this->memberName = new StringField(
            $rawPrediction["member_name"],
            $pageId
        );
        if (!isset($rawPrediction["payer_id"])) {
            throw new MindeeUnsetException();
        }
        $this->payerId = new StringField(
            $rawPrediction["payer_id"],
            $pageId
        );
        if (!isset($rawPrediction["rx_bin"])) {
            throw new MindeeUnsetException();
        }
        $this->rxBin = new StringField(
            $rawPrediction["rx_bin"],
            $pageId
        );
        if (!isset($rawPrediction["rx_grp"])) {
            throw new MindeeUnsetException();
        }
        $this->rxGrp = new StringField(
            $rawPrediction["rx_grp"],
            $pageId
        );
        if (!isset($rawPrediction["rx_pcn"])) {
            throw new MindeeUnsetException();
        }
        $this->rxPcn = new StringField(
            $rawPrediction["rx_pcn"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $dependents = implode(
            "\n             ",
            $this->dependents
        );
        $copaysSummary = strval($this->copays);

        $outStr = ":Company Name: $this->companyName
:Member Name: $this->memberName
:Member ID: $this->memberId
:Issuer 80840: $this->issuer80840
:Dependents: $dependents
:Group Number: $this->groupNumber
:Payer ID: $this->payerId
:RX BIN: $this->rxBin
:RX GRP: $this->rxGrp
:RX PCN: $this->rxPcn
:copays: $copaysSummary
:Enrollment Date: $this->enrollmentDate
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
