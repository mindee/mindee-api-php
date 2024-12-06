<?php

namespace Mindee\Input;

use Mindee\Error\ErrorCode;
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
            throw new MindeeSourceException(
                'URL must be HTTPS',
                ErrorCode::USER_INPUT_ERROR
            );
        }
        $this->url = $url;
    }
    /**
     * Downloads the file from the url, and returns a BytesInput wrapper object for it.
     *
     * @param string|null $filename     Name of the file.
     * @param string|null $username     Optional username for credential-based authentication.
     * @param string|null $password     Optional password for credential-based authentication.
     * @param string|null $token        Optional token for JWT-based authentication.
     * @param integer     $maxRedirects Maximum amount of redirects to follow.
     * @return BytesInput
     * @throws MindeeSourceException    Throws if the file can't be accessed, downloaded or converted to a proper input
     * source.
     */
    public function asLocalInputSource(
        ?string $filename = null,
        ?string $username = null,
        ?string $password = null,
        ?string $token = null,
        int $maxRedirects = 3
    ): BytesInput {
        $filename = $filename ?? basename(parse_url($this->url, PHP_URL_PATH));
        if ($filename === '' || !pathinfo($filename, PATHINFO_EXTENSION)) {
            throw new MindeeSourceException(
                'Filename must end with an extension.',
                ErrorCode::USER_INPUT_ERROR
            );
        }

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $maxRedirects);

        if ($token !== null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
        } elseif ($username !== null && $password !== null) {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new MindeeSourceException(
                'Failed to download file: ' . $error,
                ErrorCode::NETWORK_ERROR
            );
        }

        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return new BytesInput($response, $filename);
        }

        throw new MindeeSourceException(
            'Failed to download file: HTTP status code ' . $httpCode,
            ErrorCode::NETWORK_ERROR
        );
    }
}
