<?php

namespace Mindee\Parsing\Standard;

use ArrayObject;

class TaxField extends BaseField
{
    use FieldPositionMixin;

    public ?float $value;
    public ?float $rate;
    public ?string $code;
    public ?float $basis;

    public function __construct(
        array $raw_prediction,
        ?int $page_id = null,
        bool $reconstructed = false,
        string $value_key = 'value'
    ) {
        parent::__construct($raw_prediction, $page_id, $reconstructed, $value_key);
        $this->setPosition($raw_prediction);
        if (array_key_exists('value', $raw_prediction) && is_numeric($raw_prediction['value'])) {
            $this->value = floatval($raw_prediction['value']);
        } else {
            $this->value = null;
            $this->confidence = 0.0;
        }
        if (array_key_exists('rate', $raw_prediction) && is_numeric($raw_prediction['rate'])) {
            $this->rate = floatval($raw_prediction['rate']);
        } else {
            $this->rate = null;
        }
        if (array_key_exists('code', $raw_prediction) && is_scalar($raw_prediction['code']) && $raw_prediction['code'] != 'N/A') {
            $this->code = strval($raw_prediction['code']);
        } else {
            $this->code = null;
        }
        if (array_key_exists('base', $raw_prediction) && is_numeric($raw_prediction['base'])) {
            $this->basis = floatval($raw_prediction['base']);
        } else {
            $this->basis = null;
        }
    }

    private function printableValues(): array
    {
        return [
            'code' => isset($this->code) ? $this->code : '',
            'basis' => strval($this->basis),
            'rate' => strval($this->rate),
            'value' => strval($this->value),
        ];
    }

    public function toTableLine(): string
    {
        $printable = $this->printableValues();

        return '| '.str_pad($printable['basis'], 13, ' ').
            ' | '.str_pad($printable['code'], 6, ' ').
            ' | '.str_pad($printable['rate'], 8, ' ').
            ' | '.str_pad($printable['rate'], 13, ' ').' |';
    }

    public function __toString(): string
    {
        $printable = $this->printableValues();

        return 'Base: '.$printable['basis'].'. ,'.
        'Code: '.$printable['code'].', '.
        'Rate (%): '.$printable['rate'].', '.
        'Amount: '.$printable['value'].', ';
    }
}

class Taxes extends ArrayObject
{
    public function __construct(array $raw_prediction, ?int $page_id)
    {
        $entries = [];
        foreach ($raw_prediction as $entry) {
            array_push($entries, new TaxField($entry, $page_id = $page_id));
        }
        parent::__construct($entries);
    }

    private static function lineSeparator(string $char): string
    {
        $out_str = '';
        $out_str .= '+'.str_repeat($char, 15);
        $out_str .= '+'.str_repeat($char, 8);
        $out_str .= '+'.str_repeat($char, 10);
        $out_str .= '+'.str_repeat($char, 15);

        return $out_str.'+';
    }

    public function __toString()
    {
        $out_str = '';
        $out_str .= "\n".Taxes::lineSeparator('-')."\n";
        $out_str .= "  | Base          | Code   | Rate (%) | Amount        |\n";
        $out_str .= Taxes::lineSeparator('=');
        $arr = [];
        foreach ($this as $entry) {
            array_push($arr, "\n  ".$entry->toTableLine()."\n".Taxes::lineSeparator('='));
        }
        $out_str .= implode("\n", $arr);

        return $out_str;
    }
}
