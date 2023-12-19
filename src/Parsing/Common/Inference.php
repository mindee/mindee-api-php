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
    public static string $endpointName;
    /**
     * @var string Version of the product's endpoint.
     */
    public static string $endpointVersion;
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
     * @param array        $rawInference Raw inference array.
     * @param integer|null $pageId       Page number for multi pages document.
     */
    public function __construct(array $rawInference, ?int $pageId = null)
    {
        $this->isRotationApplied = null;
        if (array_key_exists('is_rotation_applied', $rawInference)) {
            $this->isRotationApplied = $rawInference['is_rotation_applied'];
        }
        $this->product = new Product($rawInference['product']);
        if (isset($pageId)) {
            $this->pageId = $pageId;
        }
    }


    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $rotationApplied = $this->isRotationApplied ? 'Yes' : 'No';
        $pagesStr = "";
        if (count($this->pages)) {
            $pagesStr = "\nPage Predictions\n================\n\n" . implode(
                "\n",
                array_map(fn ($page) => strval($page), $this->pages)
            );
        }

        return "Inference
#########
:Product: $this->product
:Rotation applied: $rotationApplied

Prediction
==========
$this->prediction$pagesStr";
    }
}
