<?php

namespace Mindee\Http;

use Mindee\Error\MindeeException;
use const Mindee\VERSION;

const API_KEY_ENV_NAME = 'MINDEE_API_KEY';

const BASE_URL_ENV_NAME = 'MINDEE_BASE_URL';
const BASE_URL_DEFAULT = 'https://api.mindee.net/v1';

const REQUEST_TIMEOUT_ENV_NAME = 'MINDEE_REQUEST_TIMEOUT';
const TIMEOUT_DEFAULT = 120;
include_once('src/version.php');
const USER_AGENT = 'mindee-api-php@v' . VERSION . ' php-v' . PHP_VERSION . ' ' . PHP_OS;

class MindeeApi
{
    public ?string $apiKey;
    public int $requestTimeout;
    public string $endpointName;
    public string $version;
    public string $accountName;
    public string $urlRoot;
    public string $baseUrl;

    private function setBaseUrl(string $value)
    {
        $this->baseUrl = $value;
    }

    private function setTimeout(string $value)
    {
        $this->requestTimeout = $value;
    }

    private function setFromEnv()
    {
        $env_vars = [
            BASE_URL_ENV_NAME => [$this, 'setBaseUrl'],
            REQUEST_TIMEOUT_ENV_NAME => [$this, 'setTimeout'],
        ];
        foreach ($env_vars as $key => $func) {
            $env_val = getenv($key) != false ? getenv($key) : '';
            if ($env_val) {
                call_user_func($func, $env_val);
                error_log('Value ' . $key . ' was set from env.');
            }
        }
    }

    private function setApiKey(?string $apiKey = null)
    {
        $envVal = getenv(API_KEY_ENV_NAME) == false ? '' : getenv(API_KEY_ENV_NAME);
        if (!$apiKey) {
            error_log('API key set from envonment');
            $this->apiKey = $envVal;
        }
        $this->apiKey = $apiKey;
    }

    public function __construct(?string $apiKey, string $endpointName, string $accountName, string $version)
    {
        $this->setApiKey($apiKey);
        if (!$this->apiKey) {
            throw new MindeeException("Missing API key for '$endpointName v$version' (belonging to $accountName), check your Client configuration.
You can set this using the " . API_KEY_ENV_NAME . ' environment variable.');
        }
        $this->endpointName = $endpointName;
        $this->accountName = $accountName;
        $this->version = $version;
        $this->baseUrl = BASE_URL_DEFAULT;
        $this->requestTimeout = TIMEOUT_DEFAULT;
        $this->setFromEnv();
        $this->urlRoot = rtrim($this->baseUrl, "/") . "/products/$this->accountName/$this->endpointName/v$this->version";
    }
}
