<?php

namespace Mindee\Product\ProofOfAddress;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\CompanyRegistrationField;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\LocaleField;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for Proof of Address, API version 1.
 */
class ProofOfAddressV1Document extends Prediction
{
    /**
    * @var DateField|null The date the document was issued.
    */
    public ?DateField $date;
    /**
    * @var DateField[]|null List of dates found on the document.
    */
    public ?array $dates;
    /**
    * @var StringField|null The address of the document's issuer.
    */
    public ?StringField $issuerAddress;
    /**
    * @var CompanyRegistrationField[]|null List of company registrations found for the issuer.
    */
    public ?array $issuerCompanyRegistration;
    /**
    * @var StringField|null The name of the person or company issuing the document.
    */
    public ?StringField $issuerName;
    /**
    * @var LocaleField|null The locale detected on the document.
    */
    public ?LocaleField $locale;
    /**
    * @var StringField|null The address of the recipient.
    */
    public ?StringField $recipientAddress;
    /**
    * @var CompanyRegistrationField[]|null List of company registrations found for the recipient.
    */
    public ?array $recipientCompanyRegistration;
    /**
    * @var StringField|null The name of the person or company receiving the document.
    */
    public ?StringField $recipientName;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->date = new DateField(
            $rawPrediction["date"],
            $pageId
        );
        $this->dates = $rawPrediction["dates"] == null ? [] : array_map(
            fn ($prediction) => new DateField($prediction, $pageId),
            $rawPrediction["dates"]
        );
        $this->issuerAddress = new StringField(
            $rawPrediction["issuer_address"],
            $pageId
        );
        $this->issuerCompanyRegistration = $rawPrediction["issuer_company_registration"] == null ? [] : array_map(
            fn ($prediction) => new CompanyRegistrationField($prediction, $pageId),
            $rawPrediction["issuer_company_registration"]
        );
        $this->issuerName = new StringField(
            $rawPrediction["issuer_name"],
            $pageId
        );
        $this->locale = new LocaleField(
            $rawPrediction["locale"],
            $pageId
        );
        $this->recipientAddress = new StringField(
            $rawPrediction["recipient_address"],
            $pageId
        );
        $this->recipientCompanyRegistration = $rawPrediction["recipient_company_registration"] == null ? [] : array_map(
            fn ($prediction) => new CompanyRegistrationField($prediction, $pageId),
            $rawPrediction["recipient_company_registration"]
        );
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
