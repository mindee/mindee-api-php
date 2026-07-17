<?php

declare(strict_types=1);

namespace Mindee\Input;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeSourceException;

/**
 * A local or distant URL input.
 */
class UrlInputSource extends InputSource
{
    /**
     * @var string The Uniform Resource Locator.
     */
    public string $url;

    /**
     * @param string $url Input URL.
     * @throws MindeeSourceException Throws if the URL isn't valid or safe.
     */
    public function __construct(string $url)
    {
        $this->validateUrl($url);
        $this->url = $url;
    }

    /**
     * Validates that a URL is safe to fetch.
     *
     * Rejects URLs with:
     * - non-HTTPS schemes,
     * - embedded user credentials,
     * - loopback hostnames (localhost, *.localhost),
     * - literal loopback, link-local, or private-network IP addresses.
     *
     * Note: DNS resolution is not performed — a hostname that resolves to a
     * private IP will not be caught here.
     *
     * @param string $url URL to validate.
     * @throws MindeeSourceException Throws if the URL is invalid or unsafe.
     */
    private function validateUrl(string $url): void
    {
        $parsed = parse_url($url);
        if ($parsed === false) {
            throw new MindeeSourceException('Invalid URL', ErrorCode::USER_INPUT_ERROR);
        }

        if (!isset($parsed['scheme']) || strtolower($parsed['scheme']) !== 'https') {
            throw new MindeeSourceException('URL must be HTTPS', ErrorCode::USER_INPUT_ERROR);
        }

        if (!empty($parsed['user']) || !empty($parsed['pass'])) {
            throw new MindeeSourceException(
                'URL must not embed user credentials',
                ErrorCode::USER_INPUT_ERROR
            );
        }

        $host = strtolower($parsed['host'] ?? '');
        if ($host === '') {
            throw new MindeeSourceException('URL is missing a host', ErrorCode::USER_INPUT_ERROR);
        }

        // Strip IPv6 brackets
        $host = preg_replace('/^\[|]$/', '', $host);

        if (
            $host === 'localhost'
            || str_ends_with((string) $host, '.localhost')
            || $host === 'ip6-localhost'
            || $host === 'ip6-loopback'
        ) {
            throw new MindeeSourceException(
                'URL host is a loopback address',
                ErrorCode::USER_INPUT_ERROR
            );
        }

        if (
            $host === '::1'
            || preg_match('/^127\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', (string) $host)
        ) {
            throw new MindeeSourceException(
                'URL host is a loopback or private address',
                ErrorCode::USER_INPUT_ERROR
            );
        }

        if (
            preg_match('/^10\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', (string) $host)
            || preg_match('/^172\.(1[6-9]|2\d|3[01])\.\d{1,3}\.\d{1,3}$/', (string) $host)
            || preg_match('/^192\.168\.\d{1,3}\.\d{1,3}$/', (string) $host)
            || preg_match('/^169\.254\.\d{1,3}\.\d{1,3}$/', (string) $host)
        ) {
            throw new MindeeSourceException(
                'URL host is a loopback or private address',
                ErrorCode::USER_INPUT_ERROR
            );
        }
    }

    /**
     * Downloads the file from the url, and returns a BytesInput wrapper object for it.
     *
     * @param string|null $filename Name of the file.
     * @param string|null $username Optional username for credential-based authentication.
     * @param string|null $password Optional password for credential-based authentication.
     * @param string|null $token Optional token for JWT-based authentication.
     * @param integer $maxRedirects Maximum amount of redirects to follow.
     * @throws MindeeSourceException Throws if the file can't be accessed, downloaded or converted to a proper input
     *                               source.
     */
    public function asLocalInputSource(
        ?string $filename = null,
        ?string $username = null,
        ?string $password = null,
        ?string $token = null,
        int $maxRedirects = 3
    ): BytesInput {
        $filename ??= basename(parse_url($this->url, PHP_URL_PATH));
        if ($filename === '' || !pathinfo($filename, PATHINFO_EXTENSION)) {
            throw new MindeeSourceException(
                'Filename must end with an extension.',
                ErrorCode::USER_INPUT_ERROR
            );
        }

        $response = $this->downloadFile($username, $password, $token, $maxRedirects);

        return new BytesInput($response, $filename);
    }

    /**
     * Attempts to grab a file's extension.
     *
     * @param string|null $filename Initial file name.
     */
    private static function getFileExtension(?string $filename): ?string
    {
        $extension = pathinfo((string) $filename, PATHINFO_EXTENSION);
        return $extension ? "." . strtolower($extension) : null;
    }

    /**
     * Generates a unique filename.
     *
     * @param string|null $extension File extension, defaults to .tmp.
     */
    private static function generateFileName(?string $extension): string
    {
        $extension ??= ".tmp";
        $random = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 1, 8);
        return "mindee_temp_" . date('Y-m-d_H-i-s') . "_$random.$extension";
    }

    /**
     * Downloads the file and saves it to the specified path.
     *
     * @param string $path Path to save the file.
     * @param string|null $filename Optional name for the saved file.
     * @param string|null $username Optional username for credential-based authentication.
     * @param string|null $password Optional password for credential-based authentication.
     * @param string|null $token Optional token for JWT-based authentication.
     * @param integer $maxRedirects Maximum amount of redirects to follow.
     * @throws MindeeSourceException Throws if the file can't be accessed, downloaded or saved.
     */
    public function saveToFile(
        string $path,
        ?string $filename = null,
        ?string $username = null,
        ?string $password = null,
        ?string $token = null,
        int $maxRedirects = 3
    ): void {
        $filename ??= basename(parse_url($this->url, PHP_URL_PATH));
        if ($filename === '' || !pathinfo($filename, PATHINFO_EXTENSION)) {
            $filename = self::generateFileName(self::getFileExtension($filename));
        }

        $response = $this->downloadFile($username, $password, $token, $maxRedirects);

        $fullPath = rtrim($path, '/') . '/' . $filename;
        if (file_put_contents($fullPath, $response) === false) {
            throw new MindeeSourceException(
                'Failed to save file to ' . $fullPath,
                ErrorCode::FILE_CANT_SAVE
            );
        }
    }

    /**
     * Downloads the file from the URL.
     *
     * @param string|null $username Optional username for credential-based authentication.
     * @param string|null $password Optional password for credential-based authentication.
     * @param string|null $token Optional token for JWT-based authentication.
     * @param integer $maxRedirects Maximum amount of redirects to follow.
     * @throws MindeeSourceException Throws if the file can't be accessed or downloaded.
     */
    private function downloadFile(
        ?string $username = null,
        ?string $password = null,
        ?string $token = null,
        int $maxRedirects = 3
    ): string {
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
            return $response;
        }

        throw new MindeeSourceException(
            'Failed to download file: HTTP status code ' . $httpCode,
            ErrorCode::NETWORK_ERROR
        );
    }
}
