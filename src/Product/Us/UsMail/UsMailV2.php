<?php

/** US Mail V2. */

namespace Mindee\Product\Us\UsMail;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * US Mail API version 2 inference prediction.
 */
class UsMailV2 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "us_mail";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "2";

    /**
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new UsMailV2Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(UsMailV2Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}
