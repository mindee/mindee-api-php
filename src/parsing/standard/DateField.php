<?php

namespace Mindee\parsing\standard;

use DateTimeImmutable;

class DateField extends BaseField
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    public ?\DateTimeImmutable $dateObject;
    public string $value;

    public function __construct(
        array $raw_prediction,
        string $value_key = 'value',
        bool $reconstructed = false,
        ?int $page_id = null
    ) {
        parent::__construct($raw_prediction, $value_key, $reconstructed, $page_id);
        $this->setPosition($raw_prediction);

        if (isset($this->value)) {
            if (strtotime($this->value)) {
                $this->dateObject = new DateTimeImmutable(strtotime($this->value), new \DateTimeZone('UTC'));
            } else {
                $this->dateObject = null;
                $this->confidence = 0.0;
            }
        }
    }
}
