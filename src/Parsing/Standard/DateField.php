<?php

namespace Mindee\Parsing\Standard;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Mindee\Error\MindeeApiException;

/**
 * A field containing a date value.
 */
class DateField extends BaseField
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var DateTimeImmutable|null Date as a standard object.
     */
    public ?DateTimeImmutable $dateObject;

    /**
     * @var string|null The raw field value.
     */
    public $value;


    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     * @param boolean      $reconstructed Whether the field was reconstructed.
     * @param string       $valueKey      Key to use for the value.
     * @throws MindeeApiException Throws if the date can't be created from the given value.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false,
        string $valueKey = 'value'
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, $valueKey);
        $this->setPosition($rawPrediction);

        if (isset($this->value)) {
            if ($this->value) {
                try {
                    $this->dateObject = new DateTimeImmutable($this->value, new DateTimeZone('UTC'));
                } catch (Exception $e) {
                    try {
                        $this->dateObject = new DateTimeImmutable(strtotime($this->value), new DateTimeZone('UTC'));
                    } catch (Exception $e2) {
                        throw new MindeeApiException("Couldn't create date field from value '" . $this->value . "'");
                    }
                }
            } else {
                $this->dateObject = null;
                $this->confidence = 0.0;
            }
        }
    }
}
