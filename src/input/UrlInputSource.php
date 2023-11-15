<?php

namespace Mindee\input;

use Mindee\error\MindeeSourceException;

class URLInputSource
{
    private string $url;

    public function __construct($url)
    {
        if (!str_starts_with($url, 'https://')) {
            throw new MindeeSourceException('URL must be HTTPS');
        }
        $this->url = $url;
    }
}
