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
     * @param array  $locale_prediction Raw locale prediction.
     * @param string $key               Name of the prediction key.
     * @return string|null
     */
    private static function getValue(array $locale_prediction, string $key): ?string
    {
        if (!array_key_exists($key, $locale_prediction) || $locale_prediction[$key] == 'N/A') {
            return null;
        }

        return $locale_prediction[$key];
    }

    /**
     * @param array        $raw_prediction Raw prediction array.
     * @param integer|null $page_id        Page number for multi pages PDF.
     * @param boolean      $reconstructed  Whether the field was reconstructed.
     */
    public function __construct(
        array $raw_prediction,
        ?int $page_id = null,
        bool $reconstructed = false
    ) {
        $value_key = array_key_exists('value', $raw_prediction) ? 'value' : 'language';
        parent::__construct($raw_prediction, $page_id, $reconstructed, $value_key);
        $this->language = LocaleField::getValue($raw_prediction, 'language');
        $this->country = LocaleField::getValue($raw_prediction, 'country');
        $this->currency = LocaleField::getValue($raw_prediction, 'currency');
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $out_str = '';
        if (isset($this->value)) {
            $out_str .= $this->value . '; ';
        }
        if (isset($this->language)) {
            $out_str .= $this->language . '; ';
        }
        if (isset($this->country)) {
            $out_str .= $this->country . '; ';
        }
        if (isset($this->currency)) {
            $out_str .= $this->currency . '; ';
        }

        return trim($out_str);
    }
}
