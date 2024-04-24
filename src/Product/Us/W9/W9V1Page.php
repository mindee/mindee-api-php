<?php

namespace Mindee\Product\Us\W9;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\PositionField;
use Mindee\Parsing\Standard\StringField;

/**
 * W9 API version 1.0 page data.
 */
class W9V1Page extends W9V1Document
{
    /**
     * @var StringField The street address (number, street, and apt. or suite no.) of the applicant.
     */
    public StringField $address;
    /**
     * @var StringField The business name or disregarded entity name, if different from Name.
     */
    public StringField $businessName;
    /**
     * @var StringField The city, state, and ZIP code of the applicant.
     */
    public StringField $cityStateZip;
    /**
     * @var StringField The employer identification number.
     */
    public StringField $ein;
    /**
     * @var StringField Name as shown on the applicant's income tax return.
     */
    public StringField $name;
    /**
     * @var PositionField Position of the signature date on the document.
     */
    public PositionField $signatureDatePosition;
    /**
     * @var PositionField Position of the signature on the document.
     */
    public PositionField $signaturePosition;
    /**
     * @var StringField The applicant's social security number.
     */
    public StringField $ssn;
    /**
     * @var StringField The federal tax classification, which can vary depending on the revision date.
     */
    public StringField $taxClassification;
    /**
     * @var StringField Depending on revision year, among S, C, P or D for Limited Liability Company Classification.
     */
    public StringField $taxClassificationLlc;
    /**
     * @var StringField Tax Classification Other Details.
     */
    public StringField $taxClassificationOtherDetails;
    /**
     * @var StringField The Revision month and year of the W9 form.
     */
    public StringField $w9RevisionDate;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->address = new StringField(
            $rawPrediction["address"],
            $pageId
        );
        $this->businessName = new StringField(
            $rawPrediction["business_name"],
            $pageId
        );
        $this->cityStateZip = new StringField(
            $rawPrediction["city_state_zip"],
            $pageId
        );
        $this->ein = new StringField(
            $rawPrediction["ein"],
            $pageId
        );
        $this->name = new StringField(
            $rawPrediction["name"],
            $pageId
        );
        $this->signatureDatePosition = new PositionField(
            $rawPrediction["signature_date_position"],
            $pageId
        );
        $this->signaturePosition = new PositionField(
            $rawPrediction["signature_position"],
            $pageId
        );
        $this->ssn = new StringField(
            $rawPrediction["ssn"],
            $pageId
        );
        $this->taxClassification = new StringField(
            $rawPrediction["tax_classification"],
            $pageId
        );
        $this->taxClassificationLlc = new StringField(
            $rawPrediction["tax_classification_llc"],
            $pageId
        );
        $this->taxClassificationOtherDetails = new StringField(
            $rawPrediction["tax_classification_other_details"],
            $pageId
        );
        $this->w9RevisionDate = new StringField(
            $rawPrediction["w9_revision_date"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {

        $outStr = ":Name: $this->name
:SSN: $this->ssn
:Address: $this->address
:City State Zip: $this->cityStateZip
:Business Name: $this->businessName
:EIN: $this->ein
:Tax Classification: $this->taxClassification
:Tax Classification Other Details: $this->taxClassificationOtherDetails
:W9 Revision Date: $this->w9RevisionDate
:Signature Position: $this->signaturePosition
:Signature Date Position: $this->signatureDatePosition
:Tax Classification LLC: $this->taxClassificationLlc
";
        $outStr .= parent::__toString();
        return SummaryHelper::cleanOutString($outStr);
    }
}
