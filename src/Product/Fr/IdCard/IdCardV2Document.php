<?php

namespace Mindee\Product\Fr\IdCard;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Carte Nationale d'Identité API version 2.0 document data.
 */
class IdCardV2Document extends Prediction
{
    /**
     * @var StringField The alternate name of the card holder.
     */
    public StringField $alternateName;
    /**
     * @var StringField The name of the issuing authority.
     */
    public StringField $authority;
    /**
     * @var DateField The date of birth of the card holder.
     */
    public DateField $birthDate;
    /**
     * @var StringField The place of birth of the card holder.
     */
    public StringField $birthPlace;
    /**
     * @var StringField The card access number (CAN).
     */
    public StringField $cardAccessNumber;
    /**
     * @var StringField The document number.
     */
    public StringField $documentNumber;
    /**
     * @var DateField The expiry date of the identification card.
     */
    public DateField $expiryDate;
    /**
     * @var StringField The gender of the card holder.
     */
    public StringField $gender;
    /**
     * @var StringField[] The given name(s) of the card holder.
     */
    public array $givenNames;
    /**
     * @var DateField The date of issue of the identification card.
     */
    public DateField $issueDate;
    /**
     * @var StringField The Machine Readable Zone, first line.
     */
    public StringField $mrz1;
    /**
     * @var StringField The Machine Readable Zone, second line.
     */
    public StringField $mrz2;
    /**
     * @var StringField The Machine Readable Zone, third line.
     */
    public StringField $mrz3;
    /**
     * @var StringField The nationality of the card holder.
     */
    public StringField $nationality;
    /**
     * @var StringField The surname of the card holder.
     */
    public StringField $surname;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["alternate_name"])) {
            throw new MindeeUnsetException();
        }
        $this->alternateName = new StringField(
            $rawPrediction["alternate_name"],
            $pageId
        );
        if (!isset($rawPrediction["authority"])) {
            throw new MindeeUnsetException();
        }
        $this->authority = new StringField(
            $rawPrediction["authority"],
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
        if (!isset($rawPrediction["card_access_number"])) {
            throw new MindeeUnsetException();
        }
        $this->cardAccessNumber = new StringField(
            $rawPrediction["card_access_number"],
            $pageId
        );
        if (!isset($rawPrediction["document_number"])) {
            throw new MindeeUnsetException();
        }
        $this->documentNumber = new StringField(
            $rawPrediction["document_number"],
            $pageId
        );
        if (!isset($rawPrediction["expiry_date"])) {
            throw new MindeeUnsetException();
        }
        $this->expiryDate = new DateField(
            $rawPrediction["expiry_date"],
            $pageId
        );
        if (!isset($rawPrediction["gender"])) {
            throw new MindeeUnsetException();
        }
        $this->gender = new StringField(
            $rawPrediction["gender"],
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
        $givenNames = implode(
            "\n                ",
            $this->givenNames
        );

        $outStr = ":Nationality: $this->nationality
:Card Access Number: $this->cardAccessNumber
:Document Number: $this->documentNumber
:Given Name(s): $givenNames
:Surname: $this->surname
:Alternate Name: $this->alternateName
:Date of Birth: $this->birthDate
:Place of Birth: $this->birthPlace
:Gender: $this->gender
:Expiry Date: $this->expiryDate
:Mrz Line 1: $this->mrz1
:Mrz Line 2: $this->mrz2
:Mrz Line 3: $this->mrz3
:Date of Issue: $this->issueDate
:Issuing Authority: $this->authority
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
