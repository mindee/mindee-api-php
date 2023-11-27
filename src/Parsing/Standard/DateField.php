<?php

namespace Mindee\Parsing\Standard;

use DateTimeImmutable;
use Mindee\Error\MindeeApiException;

class DateField extends BaseField
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    public ?\DateTimeImmutable $dateObject;
    public $value;

    public function __construct(
        array $raw_prediction,
        ?int $page_id = null,
        bool $reconstructed = false,
        string $value_key = 'value'
    ) {
        parent::__construct($raw_prediction, $page_id, $reconstructed, $value_key);
        $this->setPosition($raw_prediction);

        if (isset($this->value)) {
            if (strtotime($this->value)) {
                try {
                    $this->dateObject = new \DateTimeImmutable($this->value, new \DateTimeZone('UTC'));
                } catch (\Exception $e) {
                    throw new MindeeApiException("Couldn't create date field from value '" . $this->value . "'");
                }
            } else {
                $this->dateObject = null;
                $this->confidence = 0.0;
            }
        }
    }
}
