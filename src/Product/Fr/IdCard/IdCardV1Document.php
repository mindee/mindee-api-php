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
     *  The name of the issuing authority.
     */
    public StringField $authority;
    /**
     *  The date of birth of the card holder.
     */
    public DateField $birthDate;
    /**
     *  The place of birth of the card holder.
     */
    public StringField $birthPlace;
    /**
     *  The place of birth of the card holder.
     */
    public DateField $expiryDate;
    /**
     *  The gender of the card holder.
     */
    public StringField $gender;
    /**
     *  The given name(s) of the card holder.
     */
    public array $givenNames;
    /**
     * The identification card number.
     */
    public StringField $idNumber;
    /**
     * Machine Readable Zone, first line
     */
    public StringField $mrz1;
    /**
     * Machine Readable Zone, second line
     */
    public StringField $mrz2;
    /**
     * The surname of the card holder.
     */
    public StringField $surname;

    function __construct(array $raw_prediction, ?int $page_id = null)
    {
        $this->authority = new StringField($raw_prediction['authority'], $page_id);
        $this->birthDate = new DateField($raw_prediction['birth_date'], $page_id);
        $this->birthPlace = new StringField($raw_prediction['birth_place'], $page_id);
        $this->expiryDate = new DateField($raw_prediction['expiry_date'], $page_id);
        $this->gender = new StringField($raw_prediction['gender'], $page_id);
        $this->givenNames = [];
        foreach ($raw_prediction['given_names'] as $prediction) {
            $this->givenNames[] = new StringField($prediction, $page_id);
        }
        $this->idNumber = new StringField($raw_prediction['id_number'], $page_id);
        $this->mrz1 = new StringField($raw_prediction['mrz1'], $page_id);
        $this->mrz2 = new StringField($raw_prediction['mrz2'], $page_id);
        $this->surname = new StringField($raw_prediction['surname'], $page_id);
    }

    function __toString():string
    {
        $given_names = implode("\n".str_repeat(" ", 15), $this->givenNames);
        $out_str = ":Identity Number: $this->idNumber\n";
        $out_str .= ":Given Name(s): $given_names\n";
        $out_str .= ":Surname: $this->surname\n";
        $out_str .= ":Date of Birth: $this->birthDate\n";
        $out_str .= ":Place of Birth: $this->birthPlace\n";
        $out_str .= ":Expiry Date: $this->expiryDate\n";
        $out_str .= ":Issuing Authority: $this->authority\n";
        $out_str .= ":Gender: $this->gender\n";
        $out_str .= ":MRZ Line 1: $this->mrz1\n";
        $out_str .= ":MRZ Line 2: $this->mrz2\n";
        return trim($out_str);
    }
}

