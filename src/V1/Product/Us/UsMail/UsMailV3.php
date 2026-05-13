<?php

declare(strict_types=1);

/** US Mail V3. */

namespace Mindee\V1\Product\Us\UsMail;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * US Mail API version 3 inference prediction.
 */
class UsMailV3 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "us_mail";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "3";

    /**
     * @param array<string, mixed> $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new UsMailV3Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(UsMailV3Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
