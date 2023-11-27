<?php

namespace Mindee\Parsing\Standard;

class PaymentDetailsField extends BaseField
{
    use FieldPositionMixin;

    public ?string $accountNumber;
    public ?string $iban;
    public ?string $routingNumber;
    public ?string $swift;

    private function getValue($raw_prediction, $key): ?string
    {
        $value = null;
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
