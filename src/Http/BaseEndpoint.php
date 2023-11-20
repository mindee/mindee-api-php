<?php

namespace Mindee\Http;

abstract class BaseEndpoint
{
    public MindeeApi $settings;

    public function __construct(MindeeApi $settings)
    {
        $this->settings = $settings;
    }
}
