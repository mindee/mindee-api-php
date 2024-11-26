<?php

/**
 * Settings and variables linked to endpoint calling & API usage.
 */

namespace Mindee\Http;

use Mindee\Client;
use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeException;

/**
 * Data class containing settings for endpoints.
 */
class MindeeApi extends BaseApi
{
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
        parent::__construct($apiKey);
        if (!$this->apiKey || strlen($this->apiKey) == 0) {
            throw new MindeeException(
                "Missing API key for '$endpointName v$version' (belonging to $accountName)," .
                " check your Client configuration.You can set this using the " .
                API_KEY_ENV_NAME . ' environment variable.',
                ErrorCode::USER_INPUT_ERROR
            );
        }
        $this->endpointName = $endpointName;
        $this->accountName = $accountName;
        $this->version = $version;
        $this->urlRoot = rtrim(
            $this->baseUrl,
            "/"
        ) . "/products/$this->accountName/$this->endpointName/v$this->version";
    }
}
