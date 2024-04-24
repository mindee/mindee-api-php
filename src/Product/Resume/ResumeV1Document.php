<?php

namespace Mindee\Product\Resume;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\ClassificationField;
use Mindee\Parsing\Standard\StringField;

/**
 * Resume API version 1.0 document data.
 */
class ResumeV1Document extends Prediction
{
    /**
     * @var StringField The location information of the candidate, including city, state, and country.
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
     * @var ClassificationField The type of the document sent.
     */
    public ClassificationField $documentType;
    /**
     * @var ResumeV1Educations The list of the candidate's educational background.
     */
    public ResumeV1Educations $education;
    /**
     * @var StringField The email address of the candidate.
     */
    public StringField $emailAddress;
    /**
     * @var StringField[] The candidate's first or given names.
     */
    public array $givenNames;
    /**
     * @var StringField[] The list of the candidate's technical abilities and knowledge.
     */
    public array $hardSkills;
    /**
     * @var StringField The position that the candidate is applying for.
     */
    public StringField $jobApplied;
    /**
     * @var ResumeV1Languages The list of languages that the candidate is proficient in.
     */
    public ResumeV1Languages $languages;
    /**
     * @var StringField The ISO 3166 code for the country of citizenship of the candidate.
     */
    public StringField $nationality;
    /**
     * @var StringField The phone number of the candidate.
     */
    public StringField $phoneNumber;
    /**
     * @var StringField The candidate's current profession.
     */
    public StringField $profession;
    /**
     * @var ResumeV1ProfessionalExperiences The list of the candidate's professional experiences.
     */
    public ResumeV1ProfessionalExperiences $professionalExperiences;
    /**
     * @var ResumeV1SocialNetworksUrls The list of social network profiles of the candidate.
     */
    public ResumeV1SocialNetworksUrls $socialNetworksUrls;
    /**
     * @var StringField[] The list of the candidate's interpersonal and communication abilities.
     */
    public array $softSkills;
    /**
     * @var StringField[] The candidate's last names.
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
        $this->documentType = new ClassificationField(
            $rawPrediction["document_type"],
            $pageId
        );
        if (!isset($rawPrediction["education"])) {
            throw new MindeeUnsetException();
        }
        $this->education = new ResumeV1Educations(
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
