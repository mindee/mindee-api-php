<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Fr\HealthCard;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Prediction;
use Mindee\V1\Parsing\Standard\DateField;
use Mindee\V1\Parsing\Standard\StringField;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Health Card API version 1.0 document data.
 */
class HealthCardV1Document extends Prediction
{
    /**
     * @var StringField[] The given names of the card holder.
     */
    public array $givenNames;
    /**
     * @var DateField The date when the carte vitale document was issued.
     */
    public DateField $issuanceDate;
    /**
     * @var StringField The social security number of the card holder.
     */
    public StringField $socialSecurity;
    /**
     * @var StringField The surname of the card holder.
     */
    public StringField $surname;
    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["given_names"])) {
            throw new MindeeUnsetException();
        }
        $this->givenNames = array_map(
            static fn($prediction) => new StringField($prediction, $pageId),
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
        return SummaryHelperV1::cleanOutString($outStr);
    }
}
