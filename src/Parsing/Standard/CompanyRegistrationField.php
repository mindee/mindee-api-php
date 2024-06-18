<?php

namespace Mindee\Parsing\Standard;

use Mindee\Parsing\Common\SummaryHelper;

/**
 * A company registration item.
 */
class CompanyRegistrationField extends BaseField
{
    use FieldPositionMixin;

    /**
     * @var string|mixed The type of registration.
     */
    public string $type;


    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     * @param boolean      $reconstructed Whether the field was reconstructed.
     * @param string       $valueKey      Key to use for the value.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false,
        string $valueKey = 'value'
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, $valueKey);
        $this->type = $rawPrediction['type'];
        $this->setPosition($rawPrediction);
    }

    /**
     * Return as a table line for RST display.
     *
     * @return string
     */
    public function toTableLine(): string
    {
        $printable = $this->printableValues();
        return sprintf("| %-15s | %-20s ", $printable['type'], $printable['value']);
    }

    /**
     * String representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        $printable = $this->printableValues();
        return sprintf("Type: %s, Value: %s", $printable['type'], $printable['value']);
    }

    /**
     * Returns an array of proper values for the formatting.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $printable = [];
        $printable['type'] = SummaryHelper::formatForDisplay($this->type);
        $printable['value'] = SummaryHelper::formatForDisplay($this->value);
        return $printable;
    }
}
