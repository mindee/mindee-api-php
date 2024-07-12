<?php

/**
 * Settings and variables linked to endpoint calling & API usage.
 */

namespace Mindee\Http;

use Mindee\Client;
use Mindee\Error\MindeeException;

use const Mindee\VERSION;

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
const USER_AGENT = 'mindee-api-php@v' . VERSION . ' php-v' . PHP_VERSION . ' ' . PHP_OS;

/**
 * Data class containing settings for endpoints.
 */
class MindeeApi
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
     * @var string Name of the endpoint.
     */
    public string $endpointName;
    /**
     * @var string Version of the endpoint.
     */
    public string $version;
    /**
     * @var string Name of the owner of an endpoint. Is equals to 'mindee' for off-the-shelf APIs.
     */
    public string $accountName;
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
    private function setBaseUrl(string $value)
    {
        $this->baseUrl = $value;
    }

    /**
     * Sets the default timeout.
     *
     * @param string $value Value for the CURL timeout.
     * @return void
     */
    private function setTimeout(string $value)
    {
        $this->requestTimeout = $value;
    }

    /**
     * Sets values from environment if needed.
     *
     * @return void
     */
    private function setFromEnv()
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
    private function setApiKey(?string $apiKey = null)
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
     * @param string|null $apiKey       API key.
     * @param string      $endpointName Name of the endpoint.
     * @param string|null $accountName  Name of the endpoint's owner.
     * @param string|null $version      Version of the endpoint.
     * @throws MindeeException Throws if the API key specified is invalid.
     */
    public function __construct(
        ?string $apiKey,
        string $endpointName,
        ?string $accountName = Client::DEFAULT_OWNER,
        ?string $version = "1"
    ) {
        $this->setApiKey($apiKey);
        if (!$this->apiKey || strlen($this->apiKey) == 0) {
            throw new MindeeException(
                "Missing API key for '$endpointName v$version' (belonging to $accountName)," .
                " check your Client configuration.You can set this using the " .
                API_KEY_ENV_NAME . ' environment variable.'
            );
        }
        $this->endpointName = $endpointName;
        $this->accountName = $accountName;
        $this->version = $version;
        $this->baseUrl = BASE_URL_DEFAULT;
        $this->requestTimeout = TIMEOUT_DEFAULT;
        $this->setFromEnv();
        $this->urlRoot = rtrim(
            $this->baseUrl,
            "/"
        ) . "/products/$this->accountName/$this->endpointName/v$this->version";
    }
}
