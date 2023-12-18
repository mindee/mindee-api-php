<?php

namespace Mindee\Http;

/**
 * Abstract class for endpoints.
 */
abstract class BaseEndpoint
{
    /**
     * @var \Mindee\Http\MindeeApi Settings of the endpoint.
     */
    public MindeeApi $settings;

    /**
     * @param \Mindee\Http\MindeeApi $settings Input settings.
     */
    public function __construct(MindeeApi $settings)
    {
        $this->settings = $settings;
    }
}
