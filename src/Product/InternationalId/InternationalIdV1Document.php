<?php

namespace Mindee\Product\InternationalId;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\ClassificationField;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for International ID, API version 1.
 */
class InternationalIdV1Document extends Prediction
{
    /**
    * @var StringField|null The physical location of the document holder's residence.
    */
    public ?StringField $address;
    /**
    * @var StringField|null The date of birth of the document holder.
    */
    public ?StringField $birthDate;
    /**
    * @var StringField|null The location where the document holder was born.
    */
    public ?StringField $birthPlace;
    /**
    * @var StringField|null The country that issued the identification document.
    */
    public ?StringField $countryOfIssue;
    /**
    * @var StringField|null The unique identifier assigned to the identification document.
    */
    public ?StringField $documentNumber;
    /**
    * @var ClassificationField|null The type of identification document being used.
    */
    public ?ClassificationField $documentType;
    /**
    * @var StringField|null The date when the document will no longer be valid for use.
    */
    public ?StringField $expiryDate;
    /**
    * @var StringField[]|null The first names or given names of the document holder.
    */
    public ?array $givenNames;
    /**
    * @var StringField|null The date when the document was issued.
    */
    public ?StringField $issueDate;
    /**
    * @var StringField|null First line of information in a standardized format for easy machine reading and processing.
    */
    public ?StringField $mrz1;
    /**
    * @var StringField|null Second line of information in a standardized format for easy machine reading and processing.
    */
    public ?StringField $mrz2;
    /**
    * @var StringField|null Third line of information in a standardized format for easy machine reading and processing.
    */
    public ?StringField $mrz3;
    /**
    * @var StringField|null Indicates the country of citizenship or nationality of the document holder.
    */
    public ?StringField $nationality;
    /**
    * @var StringField|null The document holder's biological sex, such as male or female.
    */
    public ?StringField $sex;
    /**
    * @var StringField[]|null The surnames of the document holder.
    */
    public ?array $surnames;
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
        $this->birthDate = new StringField(
            $rawPrediction["birth_date"],
            $pageId
        );
        $this->birthPlace = new StringField(
            $rawPrediction["birth_place"],
            $pageId
        );
        $this->countryOfIssue = new StringField(
            $rawPrediction["country_of_issue"],
            $pageId
        );
        $this->documentNumber = new StringField(
            $rawPrediction["document_number"],
            $pageId
        );
        $this->documentType = new ClassificationField(
            $rawPrediction["document_type"],
            $pageId
        );
        $this->expiryDate = new StringField(
            $rawPrediction["expiry_date"],
            $pageId
        );
        $this->givenNames = $rawPrediction["given_names"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["given_names"]
        );
        $this->issueDate = new StringField(
            $rawPrediction["issue_date"],
            $pageId
        );
        $this->mrz1 = new StringField(
            $rawPrediction["mrz1"],
            $pageId
        );
        $this->mrz2 = new StringField(
            $rawPrediction["mrz2"],
            $pageId
        );
        $this->mrz3 = new StringField(
            $rawPrediction["mrz3"],
            $pageId
        );
        $this->nationality = new StringField(
            $rawPrediction["nationality"],
            $pageId
        );
        $this->sex = new StringField(
            $rawPrediction["sex"],
            $pageId
        );
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
