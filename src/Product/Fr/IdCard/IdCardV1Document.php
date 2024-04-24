<?php

namespace Mindee\Product\Fr\IdCard;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Carte Nationale d'Identité API version 1.1 document data.
 */
class IdCardV1Document extends Prediction
{
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
     * @var StringField The identification card number.
     */
    public StringField $idNumber;
    /**
     * @var StringField Machine Readable Zone, first line
     */
    public StringField $mrz1;
    /**
     * @var StringField Machine Readable Zone, second line
     */
    public StringField $mrz2;
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
        if (!isset($rawPrediction["id_number"])) {
            throw new MindeeUnsetException();
        }
        $this->idNumber = new StringField(
            $rawPrediction["id_number"],
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

        $outStr = ":Identity Number: $this->idNumber
:Given Name(s): $givenNames
:Surname: $this->surname
:Date of Birth: $this->birthDate
:Place of Birth: $this->birthPlace
:Expiry Date: $this->expiryDate
:Issuing Authority: $this->authority
:Gender: $this->gender
:MRZ Line 1: $this->mrz1
:MRZ Line 2: $this->mrz2
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
