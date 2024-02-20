<?php

namespace Mindee\Product\InternationalId;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\ClassificationField;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for International ID, API version 1.
 */
class InternationalIdV1Document extends Prediction
{
    /**
     * @var StringField The physical location of the document holder's residence.
     */
    public StringField $address;
    /**
     * @var DateField The date of birth of the document holder.
     */
    public DateField $birthDate;
    /**
     * @var StringField The location where the document holder was born.
     */
    public StringField $birthPlace;
    /**
     * @var StringField The country that issued the identification document.
     */
    public StringField $countryOfIssue;
    /**
     * @var StringField The unique identifier assigned to the identification document.
     */
    public StringField $documentNumber;
    /**
     * @var ClassificationField The type of identification document being used.
     */
    public ClassificationField $documentType;
    /**
     * @var DateField The date when the document will no longer be valid for use.
     */
    public DateField $expiryDate;
    /**
     * @var StringField[] The first names or given names of the document holder.
     */
    public array $givenNames;
    /**
     * @var DateField The date when the document was issued.
     */
    public DateField $issueDate;
    /**
     * @var StringField First line of information in a standardized format for easy machine reading and processing.
     */
    public StringField $mrz1;
    /**
     * @var StringField Second line of information in a standardized format for easy machine reading and processing.
     */
    public StringField $mrz2;
    /**
     * @var StringField Third line of information in a standardized format for easy machine reading and processing.
     */
    public StringField $mrz3;
    /**
     * @var StringField Indicates the country of citizenship or nationality of the document holder.
     */
    public StringField $nationality;
    /**
     * @var StringField The document holder's biological sex, such as male or female.
     */
    public StringField $sex;
    /**
     * @var StringField[] The surnames of the document holder.
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
        if (!isset($rawPrediction["mrz1"])) {
            throw new MindeeUnsetException();
        }
        $this->mrz1 = new StringField(
            $rawPrediction["mrz1"],
            $pageId
        );
        if (!isset($rawPrediction["mrz2"])) {
            throw new MindeeUnsetException();
        }
        $this->mrz2 = new StringField(
            $rawPrediction["mrz2"],
            $pageId
        );
        if (!isset($rawPrediction["mrz3"])) {
            throw new MindeeUnsetException();
        }
        $this->mrz3 = new StringField(
            $rawPrediction["mrz3"],
            $pageId
        );
        if (!isset($rawPrediction["nationality"])) {
            throw new MindeeUnsetException();
        }
        $this->nationality = new StringField(
            $rawPrediction["nationality"],
            $pageId
        );
        if (!isset($rawPrediction["sex"])) {
            throw new MindeeUnsetException();
        }
        $this->sex = new StringField(
            $rawPrediction["sex"],
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
:Country of Issue: $this->countryOfIssue
:Surnames: $surnames
:Given Names: $givenNames
:Gender: $this->sex
:Birth date: $this->birthDate
:Birth Place: $this->birthPlace
:Nationality: $this->nationality
:Issue Date: $this->issueDate
:Expiry Date: $this->expiryDate
:Address: $this->address
:Machine Readable Zone Line 1: $this->mrz1
:Machine Readable Zone Line 2: $this->mrz2
:Machine Readable Zone Line 3: $this->mrz3
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
