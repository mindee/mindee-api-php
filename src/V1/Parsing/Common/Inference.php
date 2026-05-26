<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common;

use Mindee\V1\Parsing\Common\Extras\Extras;

use function array_key_exists;
use function count;

/**
 * Base Inference class for all predictions.
 */
abstract class Inference
{
    /**
     * @var Product Name and version of a given product, as sent back by the API.
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
     * @var Prediction A document's top-level Prediction.
     */
    public Prediction $prediction;
    /**
     * @var array<Page> A document's pages.
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
     * @var Extras|null Potential Extras fields sent back along with the prediction.
     */
    public ?Extras $extras;


    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw inference array.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawResponse, ?int $pageId = null)
    {
        $this->isRotationApplied = null;
        if (array_key_exists('is_rotation_applied', $rawResponse)) {
            $this->isRotationApplied = $rawResponse['is_rotation_applied'];
        }
        $this->product = new Product($rawResponse['product']);
        if (isset($pageId)) {
            $this->pageId = $pageId;
        }
        if (array_key_exists('extras', $rawResponse)) {
            $this->extras = new Extras($rawResponse['extras']);
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
                array_map(static fn($page) => (string) $page, $this->pages)
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
