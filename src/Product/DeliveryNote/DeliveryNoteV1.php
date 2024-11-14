<?php

/** Delivery note V1. */

namespace Mindee\Product\DeliveryNote;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

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
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}