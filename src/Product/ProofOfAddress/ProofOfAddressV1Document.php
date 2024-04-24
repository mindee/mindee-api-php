<?php

namespace Mindee\Product\ProofOfAddress;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\CompanyRegistrationField;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\LocaleField;
use Mindee\Parsing\Standard\StringField;

/**
 * Proof of Address API version 1.1 document data.
 */
class ProofOfAddressV1Document extends Prediction
{
    /**
     * @var DateField The date the document was issued.
     */
    public DateField $date;
    /**
     * @var DateField[] List of dates found on the document.
     */
    public array $dates;
    /**
     * @var StringField The address of the document's issuer.
     */
    public StringField $issuerAddress;
    /**
     * @var CompanyRegistrationField[] List of company registrations found for the issuer.
     */
    public array $issuerCompanyRegistration;
    /**
     * @var StringField The name of the person or company issuing the document.
     */
    public StringField $issuerName;
    /**
     * @var LocaleField The locale detected on the document.
     */
    public LocaleField $locale;
    /**
     * @var StringField The address of the recipient.
     */
    public StringField $recipientAddress;
    /**
     * @var CompanyRegistrationField[] List of company registrations found for the recipient.
     */
    public array $recipientCompanyRegistration;
    /**
     * @var StringField The name of the person or company receiving the document.
     */
    public StringField $recipientName;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["date"])) {
            throw new MindeeUnsetException();
        }
        $this->date = new DateField(
            $rawPrediction["date"],
            $pageId
        );
        if (!isset($rawPrediction["dates"])) {
            throw new MindeeUnsetException();
        }
        $this->dates = $rawPrediction["dates"] == null ? [] : array_map(
            fn ($prediction) => new DateField($prediction, $pageId),
            $rawPrediction["dates"]
        );
        if (!isset($rawPrediction["issuer_address"])) {
            throw new MindeeUnsetException();
        }
        $this->issuerAddress = new StringField(
            $rawPrediction["issuer_address"],
            $pageId
        );
        if (!isset($rawPrediction["issuer_company_registration"])) {
            throw new MindeeUnsetException();
        }
        $this->issuerCompanyRegistration = $rawPrediction["issuer_company_registration"] == null ? [] : array_map(
            fn ($prediction) => new CompanyRegistrationField($prediction, $pageId),
            $rawPrediction["issuer_company_registration"]
        );
        if (!isset($rawPrediction["issuer_name"])) {
            throw new MindeeUnsetException();
        }
        $this->issuerName = new StringField(
            $rawPrediction["issuer_name"],
            $pageId
        );
        if (!isset($rawPrediction["locale"])) {
            throw new MindeeUnsetException();
        }
        $this->locale = new LocaleField(
            $rawPrediction["locale"],
            $pageId
        );
        if (!isset($rawPrediction["recipient_address"])) {
            throw new MindeeUnsetException();
        }
        $this->recipientAddress = new StringField(
            $rawPrediction["recipient_address"],
            $pageId
        );
        if (!isset($rawPrediction["recipient_company_registration"])) {
            throw new MindeeUnsetException();
        }
        $this->recipientCompanyRegistration = $rawPrediction["recipient_company_registration"] == null ? [] : array_map(
            fn ($prediction) => new CompanyRegistrationField($prediction, $pageId),
            $rawPrediction["recipient_company_registration"]
        );
        if (!isset($rawPrediction["recipient_name"])) {
            throw new MindeeUnsetException();
        }
        $this->recipientName = new StringField(
            $rawPrediction["recipient_name"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $issuerCompanyRegistration = implode(
            "\n                               ",
            $this->issuerCompanyRegistration
        );
        $recipientCompanyRegistration = implode(
            "\n                                  ",
            $this->recipientCompanyRegistration
        );
        $dates = implode(
            "\n        ",
            $this->dates
        );

        $outStr = ":Locale: $this->locale
:Issuer Name: $this->issuerName
:Issuer Company Registrations: $issuerCompanyRegistration
:Issuer Address: $this->issuerAddress
:Recipient Name: $this->recipientName
:Recipient Company Registrations: $recipientCompanyRegistration
:Recipient Address: $this->recipientAddress
:Dates: $dates
:Date of Issue: $this->date
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
