<?php

namespace Mindee\Product\Fr\IdCard;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;

/**
 * Inference prediction for Carte Nationale d'Identité, API version 1.
 */
class IdCardV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpoint_name = "idcard_fr";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpoint_version = "1";

    /**
     * @param array $raw_prediction Raw prediction from the HTTP response.
     */
    public function __construct(array $raw_prediction)
    {
        parent::__construct($raw_prediction);
        $this->prediction = new IdCardV1Document($raw_prediction['prediction']);
        $this->pages = [];
        foreach ($raw_prediction['pages'] as $page) {
            $this->pages[] = new Page(IdCardV1Page::class, $page);
        }
    }
}
