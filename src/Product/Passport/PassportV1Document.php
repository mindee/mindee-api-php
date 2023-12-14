<?php

namespace Mindee\Product\Passport;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for Passport, API version 1.
 */
class PassportV1Document extends Prediction
{
    /**
    * @var DateField The date of birth of the passport holder.
    */
    public DateField $birthDate;
    /**
    * @var StringField The place of birth of the passport holder.
    */
    public StringField $birthPlace;
    /**
    * @var StringField The country's 3 letter code (ISO 3166-1 alpha-3).
    */
    public StringField $country;
    /**
    * @var DateField The expiry date of the passport.
    */
    public DateField $expiryDate;
    /**
    * @var StringField The gender of the passport holder.
    */
    public StringField $gender;
    /**
    * @var StringField[] The given name(s) of the passport holder.
    */
    public array $givenNames;
    /**
    * @var StringField The passport's identification number.
    */
    public StringField $idNumber;
    /**
    * @var DateField The date the passport was issued.
    */
    public DateField $issuanceDate;
    /**
    * @var StringField Machine Readable Zone, first line
    */
    public StringField $mrz1;
    /**
    * @var StringField Machine Readable Zone, second line
    */
    public StringField $mrz2;
    /**
    * @var StringField The surname of the passport holder.
    */
    public StringField $surname;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->birthDate = new DateField(
            $rawPrediction["birth_date"],
            $pageId
        );
        $this->birthPlace = new StringField(
            $rawPrediction["birth_place"],
            $pageId
        );
        $this->country = new StringField(
            $rawPrediction["country"],
            $pageId
        );
        $this->expiryDate = new DateField(
            $rawPrediction["expiry_date"],
            $pageId
        );
        $this->gender = new StringField(
            $rawPrediction["gender"],
            $pageId
        );
        $this->givenNames = $rawPrediction["given_names"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["given_names"]
        );
        $this->idNumber = new StringField(
            $rawPrediction["id_number"],
            $pageId
        );
        $this->issuanceDate = new DateField(
            $rawPrediction["issuance_date"],
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
        $this->surname = new StringField(
            $rawPrediction["surname"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $givenNames = implode(
            "\n                ",
            $this->givenNames
        );

        $outStr = ":Country Code: $this->country
:ID Number: $this->idNumber
:Given Name(s): $givenNames
:Surname: $this->surname
:Date of Birth: $this->birthDate
:Place of Birth: $this->birthPlace
:Gender: $this->gender
:Date of Issue: $this->issuanceDate
:Expiry Date: $this->expiryDate
:MRZ Line 1: $this->mrz1
:MRZ Line 2: $this->mrz2
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
