<?php

declare(strict_types=1);

/** Passport V1. */

namespace Mindee\V1\Product\Passport;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Passport API version 1 inference prediction.
 */
class PassportV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "passport";
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
        $this->prediction = new PassportV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(PassportV1Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
