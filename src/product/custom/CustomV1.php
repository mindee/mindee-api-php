<?php

namespace Mindee\product\custom;

use Mindee\parsing\common\Inference;
use Mindee\parsing\common\Page;

class CustomV1 extends Inference
{
    public static string $endpoint_name = "custom";
    public static string $endpoint_version = "1";

    function __construct(array $raw_prediction)
    {
        parent::__construct($raw_prediction);
        $this->prediction = new CustomV1Document($raw_prediction['prediction']);
        $this->pages = [];
        foreach ($raw_prediction['pages'] as $page) {
            $this->pages[] = new Page(CustomV1Page::class, $page);
        }
    }
}
