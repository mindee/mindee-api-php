<?php

namespace Mindee\Product\InternationalId;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\ClassificationField;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * International ID API version 2.0 document data.
 */
class InternationalIdV2Document extends Prediction
{
    /**
     * @var StringField The physical address of the document holder.
     */
    public StringField $address;
    /**
     * @var DateField The date of birth of the document holder.
     */
    public DateField $birthDate;
    /**
     * @var StringField The place of birth of the document holder.
     */
    public StringField $birthPlace;
    /**
     * @var StringField The country where the document was issued.
     */
    public StringField $countryOfIssue;
    /**
     * @var StringField The unique identifier assigned to the document.
     */
    public StringField $documentNumber;
    /**
     * @var ClassificationField The type of personal identification document.
     */
    public ClassificationField $documentType;
    /**
     * @var DateField The date when the document becomes invalid.
     */
    public DateField $expiryDate;
    /**
     * @var StringField[] The list of the document holder's given names.
     */
    public array $givenNames;
    /**
     * @var DateField The date when the document was issued.
     */
    public DateField $issueDate;
    /**
     * @var StringField The Machine Readable Zone, first line.
     */
    public StringField $mrzLine1;
    /**
     * @var StringField The Machine Readable Zone, second line.
     */
    public StringField $mrzLine2;
    /**
     * @var StringField The Machine Readable Zone, third line.
     */
    public StringField $mrzLine3;
    /**
     * @var StringField The country of citizenship of the document holder.
     */
    public StringField $nationality;
    /**
     * @var StringField The unique identifier assigned to the document holder.
     */
    public StringField $personalNumber;
    /**
     * @var StringField The biological sex of the document holder.
     */
    public StringField $sex;
    /**
     * @var StringField The state or territory where the document was issued.
     */
    public StringField $stateOfIssue;
    /**
     * @var StringField[] The list of the document holder's family names.
     */
    public array $surnames;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["address"])) {
            throw new MindeeUnsetException();
        }
        $this->address = new StringField(
            $rawPrediction["address"],
            $pageId
        );
        if (!isset($rawPrediction["birth_date"])) {
            throw new MindeeUnsetException();
        }
        $this->birthDate = new DateField(
            $rawPrediction["birth_date"],
            $pageId
        );
        if (!isset($rawPrediction["birth_place"])) {
            throw new MindeeUnsetException();
        }
        $this->birthPlace = new StringField(
            $rawPrediction["birth_place"],
            $pageId
        );
        if (!isset($rawPrediction["country_of_issue"])) {
            throw new MindeeUnsetException();
        }
        $this->countryOfIssue = new StringField(
            $rawPrediction["country_of_issue"],
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
        if (!isset($rawPrediction["expiry_date"])) {
            throw new MindeeUnsetException();
        }
        $this->expiryDate = new DateField(
            $rawPrediction["expiry_date"],
            $pageId
        );
        if (!isset($rawPrediction["given_names"])) {
            throw new MindeeUnsetException();
        }
        $this->givenNames = $rawPrediction["given_names"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["given_names"]
        );
        if (!isset($rawPrediction["issue_date"])) {
            throw new MindeeUnsetException();
        }
        $this->issueDate = new DateField(
            $rawPrediction["issue_date"],
            $pageId
        );
        if (!isset($rawPrediction["mrz_line1"])) {
            throw new MindeeUnsetException();
        }
        $this->mrzLine1 = new StringField(
            $rawPrediction["mrz_line1"],
            $pageId
        );
        if (!isset($rawPrediction["mrz_line2"])) {
            throw new MindeeUnsetException();
        }
        $this->mrzLine2 = new StringField(
            $rawPrediction["mrz_line2"],
            $pageId
        );
        if (!isset($rawPrediction["mrz_line3"])) {
            throw new MindeeUnsetException();
        }
        $this->mrzLine3 = new StringField(
            $rawPrediction["mrz_line3"],
            $pageId
        );
        if (!isset($rawPrediction["nationality"])) {
            throw new MindeeUnsetException();
        }
        $this->nationality = new StringField(
            $rawPrediction["nationality"],
            $pageId
        );
        if (!isset($rawPrediction["personal_number"])) {
            throw new MindeeUnsetException();
        }
        $this->personalNumber = new StringField(
            $rawPrediction["personal_number"],
            $pageId
        );
        if (!isset($rawPrediction["sex"])) {
            throw new MindeeUnsetException();
        }
        $this->sex = new StringField(
            $rawPrediction["sex"],
            $pageId
        );
        if (!isset($rawPrediction["state_of_issue"])) {
            throw new MindeeUnsetException();
        }
        $this->stateOfIssue = new StringField(
            $rawPrediction["state_of_issue"],
            $pageId
        );
        if (!isset($rawPrediction["surnames"])) {
            throw new MindeeUnsetException();
        }
        $this->surnames = $rawPrediction["surnames"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["surnames"]
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $surnames = implode(
            "\n           ",
            $this->surnames
        );
        $givenNames = implode(
            "\n              ",
            $this->givenNames
        );

        $outStr = ":Document Type: $this->documentType
:Document Number: $this->documentNumber
:Surnames: $surnames
:Given Names: $givenNames
:Sex: $this->sex
:Birth Date: $this->birthDate
:Birth Place: $this->birthPlace
:Nationality: $this->nationality
:Personal Number: $this->personalNumber
:Country of Issue: $this->countryOfIssue
:State of Issue: $this->stateOfIssue
:Issue Date: $this->issueDate
:Expiration Date: $this->expiryDate
:Address: $this->address
:MRZ Line 1: $this->mrzLine1
:MRZ Line 2: $this->mrzLine2
:MRZ Line 3: $this->mrzLine3
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
