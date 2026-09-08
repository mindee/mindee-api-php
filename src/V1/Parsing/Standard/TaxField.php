<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Standard;

use Mindee\V1\Parsing\SummaryHelperV1;

use function array_key_exists;
use function is_scalar;

/**
 * Tax line information.
 * @extends BaseField<float>
 */
class TaxField extends BaseField
{
    use FieldPositionMixin;
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
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
     * @param boolean $reconstructed Whether the field has been reconstructed.
     * @param string $valueKey Key to use for the value.
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
            $this->value = (float) ($rawPrediction['value']);
        } else {
            $this->value = null;
            $this->confidence = 0.0;
        }
        if (array_key_exists('rate', $rawPrediction) && is_numeric($rawPrediction['rate'])) {
            $this->rate = (float) ($rawPrediction['rate']);
        } else {
            $this->rate = null;
        }
        if (
            array_key_exists('code', $rawPrediction) && is_scalar(
                $rawPrediction['code']
            ) && $rawPrediction['code'] !== 'N/A'
        ) {
            $this->code = (string) ($rawPrediction['code']);
        } else {
            $this->code = null;
        }
        if (array_key_exists('base', $rawPrediction) && is_numeric($rawPrediction['base'])) {
            $this->basis = (float) ($rawPrediction['base']);
        } else {
            $this->basis = null;
        }
    }

    /**
     * Returns an array of immediately printable values.
     *
     * @return array<string, string> Array of printable values.
     */
    private function printableValues(): array
    {
        return [
            'code' => $this->code ?? '',
            'basis' => isset($this->basis) ? number_format((float) $this->basis, 2, ".", "") : '',
            'rate' => isset($this->rate) ? number_format((float) $this->rate, 2, ".", "") : '',
            'value' => isset($this->value) ? number_format((float) $this->value, 2, ".", "") : '',
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

        $outStr = "| ";
        $outStr .= SummaryHelperV1::padString($printable['basis'], 13);
        $outStr .= SummaryHelperV1::padString($printable['code'], 6);
        $outStr .= SummaryHelperV1::padString($printable['rate'], 8);
        $outStr .= SummaryHelperV1::padString($printable['value'], 13);
        return rtrim(SummaryHelperV1::cleanOutString($outStr));
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $printable = $this->printableValues();

        return rtrim(
            'Base: ' . $printable['basis'] . ', '
            . 'Code: ' . $printable['code'] . ', '
            . 'Rate (%): ' . $printable['rate'] . ', '
            . 'Amount: ' . $printable['value']
        );
    }
}
