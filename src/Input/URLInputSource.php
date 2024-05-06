<?php

namespace Mindee\Input;

use Mindee\Error\MindeeSourceException;

/**
 * A local or distant URL input.
 */
class URLInputSource extends InputSource
{
    /**
     * @var string The Uniform Resource Locator.
     */
    public string $url;

    /**
     * @param string $url Input URL.
     * @throws MindeeSourceException Throws if the URL isn't secure.
     */
    public function __construct(string $url)
    {
        if ((substr($url, 0, 8) !== 'https://')) {
            throw new MindeeSourceException('URL must be HTTPS');
        }
        $this->url = $url;
    }
}
