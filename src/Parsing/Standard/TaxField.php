<?php

namespace Mindee\Parsing\Standard;

use ArrayObject;

/**
 * Tax line information.
 */
class TaxField extends BaseField
{
    use FieldPositionMixin;

    /**
     * @var float|null The amount of the tax line.
     */
    public $value;
    /**
     * @var float|null The tax rate.
     */
    public ?float $rate;
    /**
     * @var string|null The tax code (HST, GST... for Canadian; City Tax, State tax for US, etc..)."
     */
    public ?string $code;
    /**
     * @var float|null The tax base.
     */
    public ?float $basis;

    /**
     * @param array        $raw_prediction Raw prediction array.
     * @param integer|null $page_id        Page number for multi pages document.
     * @param boolean      $reconstructed  Whether the field has been reconstructed.
     * @param string       $value_key      Key to use for the value.
     */
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
        if (
            array_key_exists('code', $raw_prediction) && is_scalar(
                $raw_prediction['code']
            ) && $raw_prediction['code'] != 'N/A'
        ) {
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

    /**
     * Returns an array of immediately printable values.
     *
     * @return array Array of printable values.
     */
    private function printableValues(): array
    {
        return [
            'code' => $this->code ?? '',
            'basis' => strval($this->basis),
            'rate' => strval($this->rate),
            'value' => strval($this->value),
        ];
    }

    /**
     * Returns the field as a rst-compliant table line.
     *
     * @return string Table line as a string.
     */
    public function toTableLine(): string
    {
        $printable = $this->printableValues();

        return '| ' . str_pad($printable['basis'], 13, ' ') .
            ' | ' . str_pad($printable['code'], 6, ' ') .
            ' | ' . str_pad($printable['rate'], 8, ' ') .
            ' | ' . str_pad($printable['rate'], 13, ' ') . ' |';
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $printable = $this->printableValues();

        return 'Base: ' . $printable['basis'] . '. ,' .
            'Code: ' . $printable['code'] . ', ' .
            'Rate (%): ' . $printable['rate'] . ', ' .
            'Amount: ' . $printable['value'] . ', ';
    }
}
