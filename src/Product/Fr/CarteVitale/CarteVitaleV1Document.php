<?php

namespace Mindee\Product\Fr\CarteVitale;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Carte Vitale API version 1.1 document data.
 */
class CarteVitaleV1Document extends Prediction
{
    /**
     * @var StringField[] The given name(s) of the card holder.
     */
    public array $givenNames;
    /**
     * @var DateField The date the card was issued.
     */
    public DateField $issuanceDate;
    /**
     * @var StringField The Social Security Number (Numéro de Sécurité Sociale) of the card holder
     */
    public StringField $socialSecurity;
    /**
     * @var StringField The surname of the card holder.
     */
    public StringField $surname;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["given_names"])) {
            throw new MindeeUnsetException();
        }
        $this->givenNames = $rawPrediction["given_names"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["given_names"]
        );
        if (!isset($rawPrediction["issuance_date"])) {
            throw new MindeeUnsetException();
        }
        $this->issuanceDate = new DateField(
            $rawPrediction["issuance_date"],
            $pageId
        );
        if (!isset($rawPrediction["social_security"])) {
            throw new MindeeUnsetException();
        }
        $this->socialSecurity = new StringField(
            $rawPrediction["social_security"],
            $pageId
        );
        if (!isset($rawPrediction["surname"])) {
            throw new MindeeUnsetException();
        }
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
