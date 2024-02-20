<?php

namespace Mindee\Product\Resume;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for Resume, API version 1.
 */
class ResumeV1Document extends Prediction
{
    /**
     * @var StringField The location information of the person, including city, state, and country.
     */
    public StringField $address;
    /**
     * @var ResumeV1Certificates The list of certificates obtained by the candidate.
     */
    public ResumeV1Certificates $certificates;
    /**
     * @var StringField The ISO 639 code of the language in which the document is written.
     */
    public StringField $documentLanguage;
    /**
     * @var StringField The type of the document sent, possible values being RESUME, MOTIVATION_LETTER and
     * RECOMMENDATION_LETTER.
     */
    public StringField $documentType;
    /**
     * @var ResumeV1Education The list of values that represent the educational background of an individual.
     */
    public ResumeV1Education $education;
    /**
     * @var StringField The email address of the candidate.
     */
    public StringField $emailAddress;
    /**
     * @var StringField[] The list of names that represent a person's first or given names.
     */
    public array $givenNames;
    /**
     * @var StringField[] The list of specific technical abilities and knowledge mentioned in a resume.
     */
    public array $hardSkills;
    /**
     * @var StringField The specific industry or job role that the applicant is applying for.
     */
    public StringField $jobApplied;
    /**
     * @var ResumeV1Languages The list of languages that a person is proficient in, as stated in their resume.
     */
    public ResumeV1Languages $languages;
    /**
     * @var StringField The ISO 3166 code for the country of citizenship or origin of the person.
     */
    public StringField $nationality;
    /**
     * @var StringField The phone number of the candidate.
     */
    public StringField $phoneNumber;
    /**
     * @var StringField The area of expertise or specialization in which the individual has professional experience
     * and qualifications.
     */
    public StringField $profession;
    /**
     * @var ResumeV1ProfessionalExperiences The list of values that represent the professional experiences of an
     * individual in their global resume.
     */
    public ResumeV1ProfessionalExperiences $professionalExperiences;
    /**
     * @var ResumeV1SocialNetworksUrls The list of URLs for social network profiles of the person.
     */
    public ResumeV1SocialNetworksUrls $socialNetworksUrls;
    /**
     * @var StringField[] The list of values that represent a person's interpersonal and communication abilities in a
     * global resume.
     */
    public array $softSkills;
    /**
     * @var StringField[] The list of last names provided in a resume document.
     */
    public array $surnames;
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
        if (!isset($rawPrediction["certificates"])) {
            throw new MindeeUnsetException();
        }
        $this->certificates = new ResumeV1Certificates(
            $rawPrediction["certificates"],
            $pageId
        );
        if (!isset($rawPrediction["document_language"])) {
            throw new MindeeUnsetException();
        }
        $this->documentLanguage = new StringField(
            $rawPrediction["document_language"],
            $pageId
        );
        if (!isset($rawPrediction["document_type"])) {
            throw new MindeeUnsetException();
        }
        $this->documentType = new StringField(
            $rawPrediction["document_type"],
            $pageId
        );
        if (!isset($rawPrediction["education"])) {
            throw new MindeeUnsetException();
        }
        $this->education = new ResumeV1Education(
            $rawPrediction["education"],
            $pageId
        );
        if (!isset($rawPrediction["email_address"])) {
            throw new MindeeUnsetException();
        }
        $this->emailAddress = new StringField(
            $rawPrediction["email_address"],
            $pageId
        );
        if (!isset($rawPrediction["given_names"])) {
            throw new MindeeUnsetException();
        }
        $this->givenNames = $rawPrediction["given_names"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["given_names"]
        );
        if (!isset($rawPrediction["hard_skills"])) {
            throw new MindeeUnsetException();
        }
        $this->hardSkills = $rawPrediction["hard_skills"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["hard_skills"]
        );
        if (!isset($rawPrediction["job_applied"])) {
            throw new MindeeUnsetException();
        }
        $this->jobApplied = new StringField(
            $rawPrediction["job_applied"],
            $pageId
        );
        if (!isset($rawPrediction["languages"])) {
            throw new MindeeUnsetException();
        }
        $this->languages = new ResumeV1Languages(
            $rawPrediction["languages"],
            $pageId
        );
        if (!isset($rawPrediction["nationality"])) {
            throw new MindeeUnsetException();
        }
        $this->nationality = new StringField(
            $rawPrediction["nationality"],
            $pageId
        );
        if (!isset($rawPrediction["phone_number"])) {
            throw new MindeeUnsetException();
        }
        $this->phoneNumber = new StringField(
            $rawPrediction["phone_number"],
            $pageId
        );
        if (!isset($rawPrediction["profession"])) {
            throw new MindeeUnsetException();
        }
        $this->profession = new StringField(
            $rawPrediction["profession"],
            $pageId
        );
        if (!isset($rawPrediction["professional_experiences"])) {
            throw new MindeeUnsetException();
        }
        $this->professionalExperiences = new ResumeV1ProfessionalExperiences(
            $rawPrediction["professional_experiences"],
            $pageId
        );
        if (!isset($rawPrediction["social_networks_urls"])) {
            throw new MindeeUnsetException();
        }
        $this->socialNetworksUrls = new ResumeV1SocialNetworksUrls(
            $rawPrediction["social_networks_urls"],
            $pageId
        );
        if (!isset($rawPrediction["soft_skills"])) {
            throw new MindeeUnsetException();
        }
        $this->softSkills = $rawPrediction["soft_skills"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["soft_skills"]
        );
        if (!isset($rawPrediction["surnames"])) {
            throw new MindeeUnsetException();
        }
        $this->surnames = $rawPrediction["surnames"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["surnames"]
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $givenNames = implode(
            "\n              ",
            $this->givenNames
        );
        $surnames = implode(
            "\n           ",
            $this->surnames
        );
        $socialNetworksUrlsSummary = strval($this->socialNetworksUrls);
        $languagesSummary = strval($this->languages);
        $hardSkills = implode(
            "\n              ",
            $this->hardSkills
        );
        $softSkills = implode(
            "\n              ",
            $this->softSkills
        );
        $educationSummary = strval($this->education);
        $professionalExperiencesSummary = strval($this->professionalExperiences);
        $certificatesSummary = strval($this->certificates);

        $outStr = ":Document Language: $this->documentLanguage
:Document Type: $this->documentType
:Given Names: $givenNames
:Surnames: $surnames
:Nationality: $this->nationality
:Email Address: $this->emailAddress
:Phone Number: $this->phoneNumber
:Address: $this->address
:Social Networks: $socialNetworksUrlsSummary
:Profession: $this->profession
:Job Applied: $this->jobApplied
:Languages: $languagesSummary
:Hard Skills: $hardSkills
:Soft Skills: $softSkills
:Education: $educationSummary
:Professional Experiences: $professionalExperiencesSummary
:Certificates: $certificatesSummary
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
