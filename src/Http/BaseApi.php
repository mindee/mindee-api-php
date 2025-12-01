<?php

/**
 * Settings and variables linked to all API usage.
 */

namespace Mindee\Http;

/**
 * Default key name for the API key entry in environment variables.
 */
const API_KEY_ENV_NAME = 'MINDEE_API_KEY';

/**
 * Default key name for the Base URL in environment variables.
 */
const BASE_URL_ENV_NAME = 'MINDEE_BASE_URL';

/**
 * Default URL prefix for API calls.
 */
const BASE_URL_DEFAULT = 'https://api.mindee.net/v1';

/**
 * Default key name for CURL request timeout in environment variables.
 */
const REQUEST_TIMEOUT_ENV_NAME = 'MINDEE_REQUEST_TIMEOUT';
/**
 * Default timeout value for curl requests.
 */
const TIMEOUT_DEFAULT = 120;
// phpcs:disable
include_once(dirname(__DIR__) . '/version.php');
// phpcs:enable

use const Mindee\VERSION;

/**
 * Get the User Agent to send for API calls.
 * @return string
 */
function getUserAgent(): string
{
    switch (PHP_OS_FAMILY) {
        case "Darwin":
            $os = "macos";
            break;
        default:
            $os = strtolower(PHP_OS_FAMILY);
    }
    return 'mindee-api-php@v' . VERSION . ' php-v' . PHP_VERSION . ' ' . $os;
}

/**
 * Base class for API settings.
 */
abstract class BaseApi
{
    /**
     * @var string|null API key.
     */
    public ?string $apiKey;
    /**
     * @var integer Timeout for the request, in ms.
     */
    public int $requestTimeout;
    /**
     * @var string Root of the URL to use for API calls.
     */
    public string $urlRoot;
    /**
     * @var string Base for the root url. Used for testing purposes.
     */
    public string $baseUrl;

    /**
     * Sets the base url.
     *
     * @param string $value Value for the base Url.
     * @return void
     */
    protected function setBaseUrl(string $value): void
    {
        $this->baseUrl = $value;
    }

    /**
     * Sets the default timeout.
     *
     * @param string $value Value for the CURL timeout.
     * @return void
     */
    protected function setTimeout(string $value)
    {
        $this->requestTimeout = $value;
    }

    /**
     * Sets values from environment, if needed.
     *
     * @return void
     */
    protected function setFromEnv()
    {
        $envVars = [
            BASE_URL_ENV_NAME => [$this, 'setBaseUrl'],
            REQUEST_TIMEOUT_ENV_NAME => [$this, 'setTimeout'],
        ];
        foreach ($envVars as $key => $func) {
            $envVal = getenv($key) ? getenv($key) : '';
            if ($envVal) {
                call_user_func($func, $envVal);
                error_log('Value ' . $key . ' was set from env.');
            }
        }
    }

    /**
     * Sets the API key.
     *
     * @param string|null $apiKey Optional API key.
     * @return void
     */
    protected function setApiKey(?string $apiKey = null)
    {
        $envVal = !getenv(API_KEY_ENV_NAME) ? '' : getenv(API_KEY_ENV_NAME);
        if (!$apiKey) {
            error_log('API key set from environment');
            $this->apiKey = $envVal;
        } else {
            $this->apiKey = $apiKey;
        }
    }

    /**
     * @param string|null $apiKey API key.
     */
    public function __construct(
        ?string $apiKey
    ) {
        $this->setApiKey($apiKey);
        $this->baseUrl = BASE_URL_DEFAULT;
        $this->requestTimeout = TIMEOUT_DEFAULT;
        $this->setFromEnv();
    }
}
