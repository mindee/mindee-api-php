<?php

namespace Mindee\Product\Fr\IdCard;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for Carte Nationale d'Identité, API version 1.
 */
class IdCardV1Document extends Prediction
{
    /**
     * @var \Mindee\Parsing\Standard\StringField The name of the issuing authority.
     */
    public StringField $authority;
    /**
     * @var \Mindee\Parsing\Standard\DateField The date of birth of the card holder.
     */
    public DateField $birthDate;
    /**
     * @var \Mindee\Parsing\Standard\StringField The place of birth of the card holder.
     */
    public StringField $birthPlace;
    /**
     * @var \Mindee\Parsing\Standard\DateField The place of birth of the card holder.
     */
    public DateField $expiryDate;
    /**
     * @var \Mindee\Parsing\Standard\StringField The gender of the card holder.
     */
    public StringField $gender;
    /**
     * @var array The given name(s) of the card holder.
     */
    public array $givenNames;
    /**
     * @var \Mindee\Parsing\Standard\StringField The identification card number.
     */
    public StringField $idNumber;
    /**
     * @var \Mindee\Parsing\Standard\StringField Machine Readable Zone, first line.
     */
    public StringField $mrz1;
    /**
     * @var \Mindee\Parsing\Standard\StringField Machine Readable Zone, second line.
     */
    public StringField $mrz2;
    /**
     * @var \Mindee\Parsing\Standard\StringField The surname of the card holder.
     */
    public StringField $surname;

    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->authority = new StringField($rawPrediction['authority'], $pageId);
        $this->birthDate = new DateField($rawPrediction['birth_date'], $pageId);
        $this->birthPlace = new StringField($rawPrediction['birth_place'], $pageId);
        $this->expiryDate = new DateField($rawPrediction['expiry_date'], $pageId);
        $this->gender = new StringField($rawPrediction['gender'], $pageId);
        $this->givenNames = [];
        foreach ($rawPrediction['given_names'] as $prediction) {
            $this->givenNames[] = new StringField($prediction, $pageId);
        }
        $this->idNumber = new StringField($rawPrediction['id_number'], $pageId);
        $this->mrz1 = new StringField($rawPrediction['mrz1'], $pageId);
        $this->mrz2 = new StringField($rawPrediction['mrz2'], $pageId);
        $this->surname = new StringField($rawPrediction['surname'], $pageId);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $givenNames = implode("\n" . str_repeat(" ", 15), $this->givenNames);
        $outStr = ":Identity Number: $this->idNumber\n";
        $outStr .= ":Given Name(s): $givenNames\n";
        $outStr .= ":Surname: $this->surname\n";
        $outStr .= ":Date of Birth: $this->birthDate\n";
        $outStr .= ":Place of Birth: $this->birthPlace\n";
        $outStr .= ":Expiry Date: $this->expiryDate\n";
        $outStr .= ":Issuing Authority: $this->authority\n";
        $outStr .= ":Gender: $this->gender\n";
        $outStr .= ":MRZ Line 1: $this->mrz1\n";
        $outStr .= ":MRZ Line 2: $this->mrz2\n";
        return trim($outStr);
    }
}
