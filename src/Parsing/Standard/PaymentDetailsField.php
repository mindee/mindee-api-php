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
     * @param array  $raw_prediction Raw prediction array.
     * @param string $key            Key to get the value of.
     * @return string|null
     */
    private function getValue(array $raw_prediction, string $key): ?string
    {
        if (
            array_key_exists($key, $raw_prediction) &&
            is_scalar($raw_prediction[$key])
        ) {
            $value = strval($raw_prediction[$key]);
        } else {
            $value = null;
        }
        if ($value == 'N/A') {
            $value = null;
        }

        return $value;
    }

    /**
     * @param array        $raw_prediction     Raw prediction array.
     * @param integer|null $page_id            Page number for multi pages PDF.
     * @param boolean      $reconstructed      Whether the field was reconstructed.
     * @param string       $value_key          Key to use for the value.
     * @param string       $account_number_key Key to use for the account number.
     * @param string       $iban_key           Key to use for the IBAN.
     * @param string       $routing_number_key Key to use for the routing number.
     * @param string       $swift_key          Key to use for the SWIFT code.
     */
    public function __construct(
        array $raw_prediction,
        ?int $page_id = null,
        bool $reconstructed = false,
        string $value_key = 'iban',
        string $account_number_key = 'account_number',
        string $iban_key = 'iban',
        string $routing_number_key = 'routing_number',
        string $swift_key = 'swift'
    ) {
        parent::__construct($raw_prediction, $page_id, $reconstructed, $value_key);

        $this->setPosition($raw_prediction);

        $this->accountNumber = $this->getValue($raw_prediction, $account_number_key);
        $this->routingNumber = $this->getValue($raw_prediction, $routing_number_key);
        $this->iban = $this->getValue($raw_prediction, $iban_key);
        $this->swift = $this->getValue($raw_prediction, $swift_key);
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
        if (isset($this->accountNumber)) {
            $out_str .= $this->accountNumber . '; ';
        }
        if (isset($this->iban)) {
            $out_str .= $this->iban . '; ';
        }
        if (isset($this->routingNumber)) {
            $out_str .= $this->routingNumber . '; ';
        }
        if (isset($this->swift)) {
            $out_str .= $this->swift . '; ';
        }

        return trim($out_str);
    }
}
