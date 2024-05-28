<?php

namespace Mindee\Parsing\Standard;

/**
 * The locale detected on the document.
 */
class LocaleField extends BaseField
{
    /**
     * @var string|null The ISO 639-1 code of the language.
     */
    public ?string $language;
    /**
     * @var string|null The ISO 3166-1 alpha-2 code of the country.
     */
    public ?string $country;
    /**
     * @var string|null The ISO 4217 code of the currency.
     */
    public ?string $currency;

    /**
     * @param array  $localePrediction Raw locale prediction.
     * @param string $key              Name of the prediction key.
     * @return string|null
     */
    private static function getValue(array $localePrediction, string $key): ?string
    {
        if (!array_key_exists($key, $localePrediction) || $localePrediction[$key] == 'N/A') {
            return null;
        }

        return $localePrediction[$key];
    }

    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     * @param boolean      $reconstructed Whether the field was reconstructed.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false
    ) {
        if (array_key_exists('value', $rawPrediction) && $rawPrediction['value'] !== null) {
            $valueKey = 'value';
        } else {
            $valueKey = 'language';
        }
        parent::__construct($rawPrediction, $pageId, $reconstructed, $valueKey);
        $this->language = LocaleField::getValue($rawPrediction, 'language');
        $this->country = LocaleField::getValue($rawPrediction, 'country');
        $this->currency = LocaleField::getValue($rawPrediction, 'currency');
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $outStr = '';
        if (isset($this->value)) {
            $outStr .= $this->value . '; ';
        }
        if (isset($this->language)) {
            $outStr .= $this->language . '; ';
        }
        if (isset($this->country)) {
            $outStr .= $this->country . '; ';
        }
        if (isset($this->currency)) {
            $outStr .= $this->currency . '; ';
        }

        return trim($outStr);
    }
}
