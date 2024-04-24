<?php

namespace Mindee\Product\Eu\DriverLicense;

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
     * @var StringField EU driver license holders address
     */
    public StringField $address;
    /**
     * @var StringField EU driver license holders categories
     */
    public StringField $category;
    /**
     * @var StringField Country code extracted as a string.
     */
    public StringField $countryCode;
    /**
     * @var DateField The date of birth of the document holder
     */
    public DateField $dateOfBirth;
    /**
     * @var StringField ID number of the Document.
     */
    public StringField $documentId;
    /**
     * @var DateField Date the document expires
     */
    public DateField $expiryDate;
    /**
     * @var StringField First name(s) of the driver license holder
     */
    public StringField $firstName;
    /**
     * @var StringField Authority that issued the document
     */
    public StringField $issueAuthority;
    /**
     * @var DateField Date the document was issued
     */
    public DateField $issueDate;
    /**
     * @var StringField Last name of the driver license holder.
     */
    public StringField $lastName;
    /**
     * @var StringField Machine-readable license number
     */
    public StringField $mrz;
    /**
     * @var StringField Place where the driver license holder was born
     */
    public StringField $placeOfBirth;
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
        if (!isset($rawPrediction["document_id"])) {
            throw new MindeeUnsetException();
        }
        $this->documentId = new StringField(
            $rawPrediction["document_id"],
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
        if (!isset($rawPrediction["issue_authority"])) {
            throw new MindeeUnsetException();
        }
        $this->issueAuthority = new StringField(
            $rawPrediction["issue_authority"],
            $pageId
        );
        if (!isset($rawPrediction["issue_date"])) {
            throw new MindeeUnsetException();
        }
        $this->issueDate = new DateField(
            $rawPrediction["issue_date"],
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
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {

        $outStr = ":Country Code: $this->countryCode
:Document ID: $this->documentId
:Driver License Category: $this->category
:Last Name: $this->lastName
:First Name: $this->firstName
:Date Of Birth: $this->dateOfBirth
:Place Of Birth: $this->placeOfBirth
:Expiry Date: $this->expiryDate
:Issue Date: $this->issueDate
:Issue Authority: $this->issueAuthority
:MRZ: $this->mrz
:Address: $this->address
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
