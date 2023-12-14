<?php

namespace Mindee\Product\Fr\CarteVitale;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for Carte Vitale, API version 1.
 */
class CarteVitaleV1Document extends Prediction
{
    /**
    * @var StringField[]|null The given name(s) of the card holder.
    */
    public ?array $givenNames;
    /**
    * @var DateField|null The date the card was issued.
    */
    public ?DateField $issuanceDate;
    /**
    * @var StringField|null The Social Security Number (Numéro de Sécurité Sociale) of the card holder
    */
    public ?StringField $socialSecurity;
    /**
    * @var StringField|null The surname of the card holder.
    */
    public ?StringField $surname;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->givenNames = $rawPrediction["given_names"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["given_names"]
        );
        $this->issuanceDate = new DateField(
            $rawPrediction["issuance_date"],
            $pageId
        );
        $this->socialSecurity = new StringField(
            $rawPrediction["social_security"],
            $pageId
        );
        $this->surname = new StringField(
            $rawPrediction["surname"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $givenNames = implode(
            "\n                ",
            $this->givenNames
        );

        $outStr = ":Given Name(s): $givenNames
:Surname: $this->surname
:Social Security Number: $this->socialSecurity
:Issuance Date: $this->issuanceDate
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
