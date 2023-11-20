<?php

namespace Mindee\Input;

use Mindee\Error\MindeeSourceException;

class URLInputSource extends InputSource
{
    protected string $url;

    public function __construct(string $url)
    {
        if (!str_starts_with($url, 'https://')) {
            throw new MindeeSourceException('URL must be HTTPS');
        }
        $this->url = $url;
    }
}
