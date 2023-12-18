<?php

namespace Mindee\Product\Us\DriverLicense;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for Driver License, API version 1.
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
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->address = new StringField(
            $rawPrediction["address"],
            $pageId
        );
        $this->dateOfBirth = new DateField(
            $rawPrediction["date_of_birth"],
            $pageId
        );
        $this->ddNumber = new StringField(
            $rawPrediction["dd_number"],
            $pageId
        );
        $this->dlClass = new StringField(
            $rawPrediction["dl_class"],
            $pageId
        );
        $this->driverLicenseId = new StringField(
            $rawPrediction["driver_license_id"],
            $pageId
        );
        $this->endorsements = new StringField(
            $rawPrediction["endorsements"],
            $pageId
        );
        $this->expiryDate = new DateField(
            $rawPrediction["expiry_date"],
            $pageId
        );
        $this->eyeColor = new StringField(
            $rawPrediction["eye_color"],
            $pageId
        );
        $this->firstName = new StringField(
            $rawPrediction["first_name"],
            $pageId
        );
        $this->hairColor = new StringField(
            $rawPrediction["hair_color"],
            $pageId
        );
        $this->height = new StringField(
            $rawPrediction["height"],
            $pageId
        );
        $this->issuedDate = new DateField(
            $rawPrediction["issued_date"],
            $pageId
        );
        $this->lastName = new StringField(
            $rawPrediction["last_name"],
            $pageId
        );
        $this->restrictions = new StringField(
            $rawPrediction["restrictions"],
            $pageId
        );
        $this->sex = new StringField(
            $rawPrediction["sex"],
            $pageId
        );
        $this->state = new StringField(
            $rawPrediction["state"],
            $pageId
        );
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
