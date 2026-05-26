<?php

declare(strict_types=1);

/** Carte Nationale d'Identité V2. */

namespace Mindee\V1\Product\Fr\IdCard;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Carte Nationale d'Identité API version 2 inference prediction.
 */
class IdCardV2 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "idcard_fr";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "2";

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new IdCardV2Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(IdCardV2Page::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
