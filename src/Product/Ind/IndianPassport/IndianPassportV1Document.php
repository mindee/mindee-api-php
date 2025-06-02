<?php

namespace Mindee\Product\Ind\IndianPassport;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\ClassificationField;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Passport - India API version 1.2 document data.
 */
class IndianPassportV1Document extends Prediction
{
    /**
     * @var StringField The first line of the address of the passport holder.
     */
    public StringField $address1;
    /**
     * @var StringField The second line of the address of the passport holder.
     */
    public StringField $address2;
    /**
     * @var StringField The third line of the address of the passport holder.
     */
    public StringField $address3;
    /**
     * @var DateField The birth date of the passport holder, ISO format: YYYY-MM-DD.
     */
    public DateField $birthDate;
    /**
     * @var StringField The birth place of the passport holder.
     */
    public StringField $birthPlace;
    /**
     * @var StringField ISO 3166-1 alpha-3 country code (3 letters format).
     */
    public StringField $country;
    /**
     * @var DateField The date when the passport will expire, ISO format: YYYY-MM-DD.
     */
    public DateField $expiryDate;
    /**
     * @var StringField The file number of the passport document.
     */
    public StringField $fileNumber;
    /**
     * @var ClassificationField The gender of the passport holder.
     */
    public ClassificationField $gender;
    /**
     * @var StringField The given names of the passport holder.
     */
    public StringField $givenNames;
    /**
     * @var StringField The identification number of the passport document.
     */
    public StringField $idNumber;
    /**
     * @var DateField The date when the passport was issued, ISO format: YYYY-MM-DD.
     */
    public DateField $issuanceDate;
    /**
     * @var StringField The place where the passport was issued.
     */
    public StringField $issuancePlace;
    /**
     * @var StringField The name of the legal guardian of the passport holder (if applicable).
     */
    public StringField $legalGuardian;
    /**
     * @var StringField The first line of the machine-readable zone (MRZ) of the passport document.
     */
    public StringField $mrz1;
    /**
     * @var StringField The second line of the machine-readable zone (MRZ) of the passport document.
     */
    public StringField $mrz2;
    /**
     * @var StringField The name of the mother of the passport holder.
     */
    public StringField $nameOfMother;
    /**
     * @var StringField The name of the spouse of the passport holder (if applicable).
     */
    public StringField $nameOfSpouse;
    /**
     * @var DateField The date of issue of the old passport (if applicable), ISO format: YYYY-MM-DD.
     */
    public DateField $oldPassportDateOfIssue;
    /**
     * @var StringField The number of the old passport (if applicable).
     */
    public StringField $oldPassportNumber;
    /**
     * @var StringField The place of issue of the old passport (if applicable).
     */
    public StringField $oldPassportPlaceOfIssue;
    /**
     * @var ClassificationField The page number of the passport document.
     */
    public ClassificationField $pageNumber;
    /**
     * @var StringField The surname of the passport holder.
     */
    public StringField $surname;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["address1"])) {
            throw new MindeeUnsetException();
        }
        $this->address1 = new StringField(
            $rawPrediction["address1"],
            $pageId
        );
        if (!isset($rawPrediction["address2"])) {
            throw new MindeeUnsetException();
        }
        $this->address2 = new StringField(
            $rawPrediction["address2"],
            $pageId
        );
        if (!isset($rawPrediction["address3"])) {
            throw new MindeeUnsetException();
        }
        $this->address3 = new StringField(
            $rawPrediction["address3"],
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
        if (!isset($rawPrediction["country"])) {
            throw new MindeeUnsetException();
        }
        $this->country = new StringField(
            $rawPrediction["country"],
            $pageId
        );
        if (!isset($rawPrediction["expiry_date"])) {
            throw new MindeeUnsetException();
        }
        $this->expiryDate = new DateField(
            $rawPrediction["expiry_date"],
            $pageId
        );
        if (!isset($rawPrediction["file_number"])) {
            throw new MindeeUnsetException();
        }
        $this->fileNumber = new StringField(
            $rawPrediction["file_number"],
            $pageId
        );
        if (!isset($rawPrediction["gender"])) {
            throw new MindeeUnsetException();
        }
        $this->gender = new ClassificationField(
            $rawPrediction["gender"],
            $pageId
        );
        if (!isset($rawPrediction["given_names"])) {
            throw new MindeeUnsetException();
        }
        $this->givenNames = new StringField(
            $rawPrediction["given_names"],
            $pageId
        );
        if (!isset($rawPrediction["id_number"])) {
            throw new MindeeUnsetException();
        }
        $this->idNumber = new StringField(
            $rawPrediction["id_number"],
            $pageId
        );
        if (!isset($rawPrediction["issuance_date"])) {
            throw new MindeeUnsetException();
        }
        $this->issuanceDate = new DateField(
            $rawPrediction["issuance_date"],
            $pageId
        );
        if (!isset($rawPrediction["issuance_place"])) {
            throw new MindeeUnsetException();
        }
        $this->issuancePlace = new StringField(
            $rawPrediction["issuance_place"],
            $pageId
        );
        if (!isset($rawPrediction["legal_guardian"])) {
            throw new MindeeUnsetException();
        }
        $this->legalGuardian = new StringField(
            $rawPrediction["legal_guardian"],
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
        if (!isset($rawPrediction["name_of_mother"])) {
            throw new MindeeUnsetException();
        }
        $this->nameOfMother = new StringField(
            $rawPrediction["name_of_mother"],
            $pageId
        );
        if (!isset($rawPrediction["name_of_spouse"])) {
            throw new MindeeUnsetException();
        }
        $this->nameOfSpouse = new StringField(
            $rawPrediction["name_of_spouse"],
            $pageId
        );
        if (!isset($rawPrediction["old_passport_date_of_issue"])) {
            throw new MindeeUnsetException();
        }
        $this->oldPassportDateOfIssue = new DateField(
            $rawPrediction["old_passport_date_of_issue"],
            $pageId
        );
        if (!isset($rawPrediction["old_passport_number"])) {
            throw new MindeeUnsetException();
        }
        $this->oldPassportNumber = new StringField(
            $rawPrediction["old_passport_number"],
            $pageId
        );
        if (!isset($rawPrediction["old_passport_place_of_issue"])) {
            throw new MindeeUnsetException();
        }
        $this->oldPassportPlaceOfIssue = new StringField(
            $rawPrediction["old_passport_place_of_issue"],
            $pageId
        );
        if (!isset($rawPrediction["page_number"])) {
            throw new MindeeUnsetException();
        }
        $this->pageNumber = new ClassificationField(
            $rawPrediction["page_number"],
            $pageId
        );
        if (!isset($rawPrediction["surname"])) {
            throw new MindeeUnsetException();
        }
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

        $outStr = ":Page Number: $this->pageNumber
:Country: $this->country
:ID Number: $this->idNumber
:Given Names: $this->givenNames
:Surname: $this->surname
:Birth Date: $this->birthDate
:Birth Place: $this->birthPlace
:Issuance Place: $this->issuancePlace
:Gender: $this->gender
:Issuance Date: $this->issuanceDate
:Expiry Date: $this->expiryDate
:MRZ Line 1: $this->mrz1
:MRZ Line 2: $this->mrz2
:Legal Guardian: $this->legalGuardian
:Name of Spouse: $this->nameOfSpouse
:Name of Mother: $this->nameOfMother
:Old Passport Date of Issue: $this->oldPassportDateOfIssue
:Old Passport Number: $this->oldPassportNumber
:Old Passport Place of Issue: $this->oldPassportPlaceOfIssue
:Address Line 1: $this->address1
:Address Line 2: $this->address2
:Address Line 3: $this->address3
:File Number: $this->fileNumber
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
