<?php

namespace Mindee\Parsing\Common;

use Mindee\Error\MindeeApiException;

/**
 * Base Inference class for all predictions.
 */
abstract class Inference
{
    /**
     * @var \Mindee\Parsing\Common\Product Name and version of a given product, as sent back by the API.
     */
    public Product $product;
    /**
     * @var string Name of the product's endpoint.
     */
    public static string $endpoint_name;
    /**
     * @var string Version of the product's endpoint.
     */
    public static string $endpoint_version;
    /**
     * @var \Mindee\Parsing\Common\Prediction A document's top-level Prediction.
     */
    public Prediction $prediction;
    /**
     * @var array A document's pages.
     */
    public array $pages;
    /**
     * @var boolean|null Whether the document has had any rotation applied to it.
     */
    public ?bool $isRotationApplied;
    /**
     * @var integer|null Optional page id for page-level predictions.
     */
    public ?int $pageId;

    /**
     * @param array        $raw_inference Raw inference array.
     * @param integer|null $page_id       Page number for multi pages PDF.
     */
    public function __construct(array $raw_inference, ?int $page_id = null)
    {
        $this->isRotationApplied = null;
        if (array_key_exists('is_rotation_applied', $raw_inference)) {
            $this->isRotationApplied = $raw_inference['is_rotation_applied'];
        }
        $this->product = new Product($raw_inference['product']);
        if (isset($page_id)) {
            $this->pageId = $page_id;
        }
    }


    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $rotation_applied = $this->isRotationApplied ? 'Yes' : 'No';
        $pages = $this->pages ? "\n" . implode("\n", $this->pages) : '';

        return "Inference
#########
:Product: $this->product
:Rotation applied: $rotation_applied

Prediction
==========
$this->prediction
$pages";
    }
}
