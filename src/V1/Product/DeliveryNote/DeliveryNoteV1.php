<?php

declare(strict_types=1);

/** Delivery note V1. */

namespace Mindee\V1\Product\DeliveryNote;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Delivery note API version 1 inference prediction.
 */
class DeliveryNoteV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "delivery_notes";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "1";

    /**
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new DeliveryNoteV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(DeliveryNoteV1Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
