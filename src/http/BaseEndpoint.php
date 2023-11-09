<?php

namespace Mindee\http;

abstract class BaseEndpoint
{
    public MindeeApi $settings;

    public function __construct(MindeeApi $settings)
    {
        $this->settings = $settings;
    }
}
