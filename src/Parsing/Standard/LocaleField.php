<?php

namespace Mindee\Parsing\Standard;

class LocaleField extends BaseField
{
    public ?string $language;
    public ?string $country;
    public ?string $currency;

    private static function getValue(array $locale_prediction, string $key): ?string
    {
        if (!array_key_exists($key, $locale_prediction) || $locale_prediction[$key] == 'N/A') {
            return null;
        }

        return $locale_prediction[$key];
    }

    public function __construct(
        array $raw_prediction,
        ?int  $page_id = null,
        bool  $reconstructed = false
    )
    {
        $value_key = array_key_exists('value', $raw_prediction) ? 'value' : 'language';
        parent::__construct($raw_prediction, $page_id, $reconstructed, $value_key);
        $this->language = LocaleField::getValue($raw_prediction, 'language');
        $this->country = LocaleField::getValue($raw_prediction, 'country');
        $this->currency = LocaleField::getValue($raw_prediction, 'currency');
    }

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
