<?php

namespace Mindee\Parsing\Standard;

/**
 * Information on a single payment.
 */
class PaymentDetailsField extends BaseField
{
    use FieldPositionMixin;

    /**
     * @var string|null Account number.
     */
    public ?string $accountNumber;
    /**
     * @var string|null Account IBAN.
     */
    public ?string $iban;
    /**
     * @var string|null Account routing number.
     */
    public ?string $routingNumber;
    /**
     * @var string|null Bank's SWIFT code.
     */
    public ?string $swift;

    /**
     * Gets the value of any given key.
     *
     * @param array  $rawPrediction Raw prediction array.
     * @param string $key           Key to get the value of.
     * @return string|null
     */
    private function getValue(array $rawPrediction, string $key): ?string
    {
        if (
            array_key_exists($key, $rawPrediction) &&
            is_scalar($rawPrediction[$key])
        ) {
            $value = strval($rawPrediction[$key]);
        } else {
            $value = null;
        }
        if ($value == 'N/A') {
            $value = null;
        }

        return $value;
    }

    /**
     * @param array        $rawPrediction    Raw prediction array.
     * @param integer|null $pageId           Page number for multi pages document.
     * @param boolean      $reconstructed    Whether the field was reconstructed.
     * @param string       $valueKey         Key to use for the value.
     * @param string       $accountNumberKey Key to use for the account number.
     * @param string       $ibanKey          Key to use for the IBAN.
     * @param string       $routingNumberKey Key to use for the routing number.
     * @param string       $swiftKey         Key to use for the SWIFT code.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false,
        string $valueKey = 'iban',
        string $accountNumberKey = 'account_number',
        string $ibanKey = 'iban',
        string $routingNumberKey = 'routing_number',
        string $swiftKey = 'swift'
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, $valueKey);

        $this->setPosition($rawPrediction);

        $this->accountNumber = $this->getValue($rawPrediction, $accountNumberKey);
        $this->routingNumber = $this->getValue($rawPrediction, $routingNumberKey);
        $this->iban = $this->getValue($rawPrediction, $ibanKey);
        $this->swift = $this->getValue($rawPrediction, $swiftKey);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $outStr = '';
        if (isset($this->accountNumber)) {
            $outStr .= $this->accountNumber . '; ';
        }
        if (isset($this->iban)) {
            $outStr .= $this->iban . '; ';
        }
        if (isset($this->routingNumber)) {
            $outStr .= $this->routingNumber . '; ';
        }
        if (isset($this->swift)) {
            $outStr .= $this->swift . '; ';
        }

        return trim($outStr);
    }
}
