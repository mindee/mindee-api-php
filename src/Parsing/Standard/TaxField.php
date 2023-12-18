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
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     * @param boolean      $reconstructed Whether the field has been reconstructed.
     * @param string       $valueKey      Key to use for the value.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false,
        string $valueKey = 'value'
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, $valueKey);
        $this->setPosition($rawPrediction);
        if (array_key_exists('value', $rawPrediction) && is_numeric($rawPrediction['value'])) {
            $this->value = floatval($rawPrediction['value']);
        } else {
            $this->value = null;
            $this->confidence = 0.0;
        }
        if (array_key_exists('rate', $rawPrediction) && is_numeric($rawPrediction['rate'])) {
            $this->rate = floatval($rawPrediction['rate']);
        } else {
            $this->rate = null;
        }
        if (
            array_key_exists('code', $rawPrediction) && is_scalar(
                $rawPrediction['code']
            ) && $rawPrediction['code'] != 'N/A'
        ) {
            $this->code = strval($rawPrediction['code']);
        } else {
            $this->code = null;
        }
        if (array_key_exists('base', $rawPrediction) && is_numeric($rawPrediction['base'])) {
            $this->basis = floatval($rawPrediction['base']);
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
            'basis' => isset($this->basis) ? number_format((float)$this->basis, 2, ".", "") : null,
            'rate' => isset($this->rate) ? number_format((float)$this->rate, 2, ".", "") : null,
            'value' => isset($this->value) ? number_format((float)$this->value, 2, ".", "") : null,
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
            ' | ' . str_pad($printable['value'], 13, ' ') . ' |';
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $printable = $this->printableValues();

        return rtrim(
            'Base: ' . $printable['basis'] . ', ' .
            'Code: ' . $printable['code'] . ', ' .
            'Rate (%): ' . $printable['rate'] . ', ' .
            'Amount: ' . $printable['value']
        );
    }
}
