<?php

namespace Mindee\Product\DriverLicense;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Driver License API version 1.0 document data.
 */
class DriverLicenseV1Document extends Prediction
{
    /**
     * @var StringField The category or class of the driver license.
     */
    public StringField $category;
    /**
     * @var StringField The alpha-3 ISO 3166 code of the country where the driver license was issued.
     */
    public StringField $countryCode;
    /**
     * @var DateField The date of birth of the driver license holder.
     */
    public DateField $dateOfBirth;
    /**
     * @var StringField The DD number of the driver license.
     */
    public StringField $ddNumber;
    /**
     * @var DateField The expiry date of the driver license.
     */
    public DateField $expiryDate;
    /**
     * @var StringField The first name of the driver license holder.
     */
    public StringField $firstName;
    /**
     * @var StringField The unique identifier of the driver license.
     */
    public StringField $id;
    /**
     * @var DateField The date when the driver license was issued.
     */
    public DateField $issuedDate;
    /**
     * @var StringField The authority that issued the driver license.
     */
    public StringField $issuingAuthority;
    /**
     * @var StringField The last name of the driver license holder.
     */
    public StringField $lastName;
    /**
     * @var StringField The Machine Readable Zone (MRZ) of the driver license.
     */
    public StringField $mrz;
    /**
     * @var StringField The place of birth of the driver license holder.
     */
    public StringField $placeOfBirth;
    /**
     * @var StringField Second part of the ISO 3166-2 code, consisting of two letters indicating the US State.
     */
    public StringField $state;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["category"])) {
            throw new MindeeUnsetException();
        }
        $this->category = new StringField(
            $rawPrediction["category"],
            $pageId
        );
        if (!isset($rawPrediction["country_code"])) {
            throw new MindeeUnsetException();
        }
        $this->countryCode = new StringField(
            $rawPrediction["country_code"],
            $pageId
        );
        if (!isset($rawPrediction["date_of_birth"])) {
            throw new MindeeUnsetException();
        }
        $this->dateOfBirth = new DateField(
            $rawPrediction["date_of_birth"],
            $pageId
        );
        if (!isset($rawPrediction["dd_number"])) {
            throw new MindeeUnsetException();
        }
        $this->ddNumber = new StringField(
            $rawPrediction["dd_number"],
            $pageId
        );
        if (!isset($rawPrediction["expiry_date"])) {
            throw new MindeeUnsetException();
        }
        $this->expiryDate = new DateField(
            $rawPrediction["expiry_date"],
            $pageId
        );
        if (!isset($rawPrediction["first_name"])) {
            throw new MindeeUnsetException();
        }
        $this->firstName = new StringField(
            $rawPrediction["first_name"],
            $pageId
        );
        if (!isset($rawPrediction["id"])) {
            throw new MindeeUnsetException();
        }
        $this->id = new StringField(
            $rawPrediction["id"],
            $pageId
        );
        if (!isset($rawPrediction["issued_date"])) {
            throw new MindeeUnsetException();
        }
        $this->issuedDate = new DateField(
            $rawPrediction["issued_date"],
            $pageId
        );
        if (!isset($rawPrediction["issuing_authority"])) {
            throw new MindeeUnsetException();
        }
        $this->issuingAuthority = new StringField(
            $rawPrediction["issuing_authority"],
            $pageId
        );
        if (!isset($rawPrediction["last_name"])) {
            throw new MindeeUnsetException();
        }
        $this->lastName = new StringField(
            $rawPrediction["last_name"],
            $pageId
        );
        if (!isset($rawPrediction["mrz"])) {
            throw new MindeeUnsetException();
        }
        $this->mrz = new StringField(
            $rawPrediction["mrz"],
            $pageId
        );
        if (!isset($rawPrediction["place_of_birth"])) {
            throw new MindeeUnsetException();
        }
        $this->placeOfBirth = new StringField(
            $rawPrediction["place_of_birth"],
            $pageId
        );
        if (!isset($rawPrediction["state"])) {
            throw new MindeeUnsetException();
        }
        $this->state = new StringField(
            $rawPrediction["state"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {

        $outStr = ":Country Code: $this->countryCode
:State: $this->state
:ID: $this->id
:Category: $this->category
:Last Name: $this->lastName
:First Name: $this->firstName
:Date of Birth: $this->dateOfBirth
:Place of Birth: $this->placeOfBirth
:Expiry Date: $this->expiryDate
:Issued Date: $this->issuedDate
:Issuing Authority: $this->issuingAuthority
:MRZ: $this->mrz
:DD Number: $this->ddNumber
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
