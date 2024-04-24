<?php

namespace Mindee\Product\Us\DriverLicense;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Driver License API version 1.1 document data.
 */
class DriverLicenseV1Document extends Prediction
{
    /**
     * @var StringField US driver license holders address
     */
    public StringField $address;
    /**
     * @var DateField US driver license holders date of birth
     */
    public DateField $dateOfBirth;
    /**
     * @var StringField Document Discriminator Number of the US Driver License
     */
    public StringField $ddNumber;
    /**
     * @var StringField US driver license holders class
     */
    public StringField $dlClass;
    /**
     * @var StringField ID number of the US Driver License.
     */
    public StringField $driverLicenseId;
    /**
     * @var StringField US driver license holders endorsements
     */
    public StringField $endorsements;
    /**
     * @var DateField Date on which the documents expires.
     */
    public DateField $expiryDate;
    /**
     * @var StringField US driver license holders eye colour
     */
    public StringField $eyeColor;
    /**
     * @var StringField US driver license holders first name(s)
     */
    public StringField $firstName;
    /**
     * @var StringField US driver license holders hair colour
     */
    public StringField $hairColor;
    /**
     * @var StringField US driver license holders hight
     */
    public StringField $height;
    /**
     * @var DateField Date on which the documents was issued.
     */
    public DateField $issuedDate;
    /**
     * @var StringField US driver license holders last name
     */
    public StringField $lastName;
    /**
     * @var StringField US driver license holders restrictions
     */
    public StringField $restrictions;
    /**
     * @var StringField US driver license holders gender
     */
    public StringField $sex;
    /**
     * @var StringField US State
     */
    public StringField $state;
    /**
     * @var StringField US driver license holders weight
     */
    public StringField $weight;
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
        if (!isset($rawPrediction["dl_class"])) {
            throw new MindeeUnsetException();
        }
        $this->dlClass = new StringField(
            $rawPrediction["dl_class"],
            $pageId
        );
        if (!isset($rawPrediction["driver_license_id"])) {
            throw new MindeeUnsetException();
        }
        $this->driverLicenseId = new StringField(
            $rawPrediction["driver_license_id"],
            $pageId
        );
        if (!isset($rawPrediction["endorsements"])) {
            throw new MindeeUnsetException();
        }
        $this->endorsements = new StringField(
            $rawPrediction["endorsements"],
            $pageId
        );
        if (!isset($rawPrediction["expiry_date"])) {
            throw new MindeeUnsetException();
        }
        $this->expiryDate = new DateField(
            $rawPrediction["expiry_date"],
            $pageId
        );
        if (!isset($rawPrediction["eye_color"])) {
            throw new MindeeUnsetException();
        }
        $this->eyeColor = new StringField(
            $rawPrediction["eye_color"],
            $pageId
        );
        if (!isset($rawPrediction["first_name"])) {
            throw new MindeeUnsetException();
        }
        $this->firstName = new StringField(
            $rawPrediction["first_name"],
            $pageId
        );
        if (!isset($rawPrediction["hair_color"])) {
            throw new MindeeUnsetException();
        }
        $this->hairColor = new StringField(
            $rawPrediction["hair_color"],
            $pageId
        );
        if (!isset($rawPrediction["height"])) {
            throw new MindeeUnsetException();
        }
        $this->height = new StringField(
            $rawPrediction["height"],
            $pageId
        );
        if (!isset($rawPrediction["issued_date"])) {
            throw new MindeeUnsetException();
        }
        $this->issuedDate = new DateField(
            $rawPrediction["issued_date"],
            $pageId
        );
        if (!isset($rawPrediction["last_name"])) {
            throw new MindeeUnsetException();
        }
        $this->lastName = new StringField(
            $rawPrediction["last_name"],
            $pageId
        );
        if (!isset($rawPrediction["restrictions"])) {
            throw new MindeeUnsetException();
        }
        $this->restrictions = new StringField(
            $rawPrediction["restrictions"],
            $pageId
        );
        if (!isset($rawPrediction["sex"])) {
            throw new MindeeUnsetException();
        }
        $this->sex = new StringField(
            $rawPrediction["sex"],
            $pageId
        );
        if (!isset($rawPrediction["state"])) {
            throw new MindeeUnsetException();
        }
        $this->state = new StringField(
            $rawPrediction["state"],
            $pageId
        );
        if (!isset($rawPrediction["weight"])) {
            throw new MindeeUnsetException();
        }
        $this->weight = new StringField(
            $rawPrediction["weight"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {

        $outStr = ":State: $this->state
:Driver License ID: $this->driverLicenseId
:Expiry Date: $this->expiryDate
:Date Of Issue: $this->issuedDate
:Last Name: $this->lastName
:First Name: $this->firstName
:Address: $this->address
:Date Of Birth: $this->dateOfBirth
:Restrictions: $this->restrictions
:Endorsements: $this->endorsements
:Driver License Class: $this->dlClass
:Sex: $this->sex
:Height: $this->height
:Weight: $this->weight
:Hair Color: $this->hairColor
:Eye Color: $this->eyeColor
:Document Discriminator: $this->ddNumber
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
