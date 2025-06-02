<?php

namespace Mindee\Parsing\Standard;

/**
 * A field representing an address.
 */
class AddressField extends StringField
{
    /** @var string|null Street number. */
    public ?string $streetNumber;
    /** @var string|null Street name. */
    public ?string $streetName;
    /** @var string|null PO-box number. */
    public ?string $poBox;
    /** @var string|null Additional address complement. */
    public ?string $addressComplement;
    /** @var string|null City or locality. */
    public ?string $city;
    /** @var string|null Postal / ZIP code. */
    public ?string $postalCode;
    /** @var string|null State, province or region. */
    public ?string $state;
    /** @var string|null Country. */
    public ?string $country;

    /**
     * @param array        $rawPrediction Raw prediction array as returned by the Mindee API.
     * @param integer|null $pageId        Page number for multi-page documents.
     * @param boolean      $reconstructed Whether the field was reconstructed.
     * @param string       $valueKey      Key to use for the full address value.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false,
        string $valueKey = 'value'
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, $valueKey);
        $this->streetNumber      = $rawPrediction['street_number']      ?? null;
        $this->streetName        = $rawPrediction['street_name']        ?? null;
        $this->poBox             = $rawPrediction['po_box']             ?? null;
        $this->addressComplement = $rawPrediction['address_complement'] ?? null;
        $this->city              = $rawPrediction['city']               ?? null;
        $this->postalCode        = $rawPrediction['postal_code']        ?? null;
        $this->state             = $rawPrediction['state']              ?? null;
        $this->country           = $rawPrediction['country']            ?? null;
    }

    /**
     * Prettier string representation (same semantics as StringField).
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
