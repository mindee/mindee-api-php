<?php

namespace Mindee\Product\BusinessCard;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\StringField;

/**
 * Business Card API version 1.0 document data.
 */
class BusinessCardV1Document extends Prediction
{
    /**
     * @var StringField The address of the person.
     */
    public StringField $address;
    /**
     * @var StringField The company the person works for.
     */
    public StringField $company;
    /**
     * @var StringField The email address of the person.
     */
    public StringField $email;
    /**
     * @var StringField The Fax number of the person.
     */
    public StringField $faxNumber;
    /**
     * @var StringField The given name of the person.
     */
    public StringField $firstname;
    /**
     * @var StringField The job title of the person.
     */
    public StringField $jobTitle;
    /**
     * @var StringField The lastname of the person.
     */
    public StringField $lastname;
    /**
     * @var StringField The mobile number of the person.
     */
    public StringField $mobileNumber;
    /**
     * @var StringField The phone number of the person.
     */
    public StringField $phoneNumber;
    /**
     * @var StringField[] The social media profiles of the person or company.
     */
    public array $socialMedia;
    /**
     * @var StringField The website of the person or company.
     */
    public StringField $website;
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
        if (!isset($rawPrediction["company"])) {
            throw new MindeeUnsetException();
        }
        $this->company = new StringField(
            $rawPrediction["company"],
            $pageId
        );
        if (!isset($rawPrediction["email"])) {
            throw new MindeeUnsetException();
        }
        $this->email = new StringField(
            $rawPrediction["email"],
            $pageId
        );
        if (!isset($rawPrediction["fax_number"])) {
            throw new MindeeUnsetException();
        }
        $this->faxNumber = new StringField(
            $rawPrediction["fax_number"],
            $pageId
        );
        if (!isset($rawPrediction["firstname"])) {
            throw new MindeeUnsetException();
        }
        $this->firstname = new StringField(
            $rawPrediction["firstname"],
            $pageId
        );
        if (!isset($rawPrediction["job_title"])) {
            throw new MindeeUnsetException();
        }
        $this->jobTitle = new StringField(
            $rawPrediction["job_title"],
            $pageId
        );
        if (!isset($rawPrediction["lastname"])) {
            throw new MindeeUnsetException();
        }
        $this->lastname = new StringField(
            $rawPrediction["lastname"],
            $pageId
        );
        if (!isset($rawPrediction["mobile_number"])) {
            throw new MindeeUnsetException();
        }
        $this->mobileNumber = new StringField(
            $rawPrediction["mobile_number"],
            $pageId
        );
        if (!isset($rawPrediction["phone_number"])) {
            throw new MindeeUnsetException();
        }
        $this->phoneNumber = new StringField(
            $rawPrediction["phone_number"],
            $pageId
        );
        if (!isset($rawPrediction["social_media"])) {
            throw new MindeeUnsetException();
        }
        $this->socialMedia = $rawPrediction["social_media"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["social_media"]
        );
        if (!isset($rawPrediction["website"])) {
            throw new MindeeUnsetException();
        }
        $this->website = new StringField(
            $rawPrediction["website"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $socialMedia = implode(
            "\n               ",
            $this->socialMedia
        );

        $outStr = ":Firstname: $this->firstname
:Lastname: $this->lastname
:Job Title: $this->jobTitle
:Company: $this->company
:Email: $this->email
:Phone Number: $this->phoneNumber
:Mobile Number: $this->mobileNumber
:Fax Number: $this->faxNumber
:Address: $this->address
:Website: $this->website
:Social Media: $socialMedia
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
