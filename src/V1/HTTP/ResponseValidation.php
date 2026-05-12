<?php

declare(strict_types=1);

/**
 * Settings and variables linked to endpoint calling & API usage.
 */

namespace Mindee\V1\HTTP;

use function count;
use function is_string;

/**
 * Wrapper class for http requests/responses validation handling.
 */
class ResponseValidation
{
    /**
     * Checks if the synchronous response is valid. Returns True if the response is valid.
     *
     * @param array $response A response object.
     * @return boolean
     */
    public static function isValidSyncResponse(array $response): bool
    {
        if (!isset($response['code']) || !is_numeric($response['code'])) {
            return false;
        }
        $statusCode = $response['code'];
        return !(
            is_nan($statusCode)
            || (int) $statusCode < 200
            || (int) $statusCode > 302
        );
    }


    /**
     * Checks if the workflow response is valid. Also checks if it is a valid synchronous response.
     * Returns True if the response is valid.
     *
     * @param array $response A response array.
     * @return boolean
     */
    public static function isValidWorkflowResponse(array $response): bool
    {
        if (!self::isValidSyncResponse($response)) {
            return false;
        }
        if (isset($response["code"])) {
            if ($response["code"] < 200 || $response["code"] > 302) {
                return false;
            }
        } else {
            return false;
        }
        if (!isset($response["data"]["execution"])) {
            return false;
        }
        return !(isset($response["data"]["execution"]["error"]) && count($response["data"]["execution"]["error"]) > 0);
    }

    /**
     * Checks if the asynchronous response is valid. Also checks if it is a valid synchronous response.
     * Returns True if the response is valid.
     *
     * @param array $response A response array.
     * @return boolean
     */
    public static function isValidAsyncResponse(array $response): bool
    {
        if (!self::isValidSyncResponse($response)) {
            return false;
        }
        if (isset($response["code"])) {
            if ($response["code"] < 200 || $response["code"] > 302) {
                return false;
            }
        } else {
            return false;
        }
        if (!isset($response["data"]["job"])) {
            return false;
        }
        return !(isset($response["data"]["job"]["error"]) && count($response["data"]["job"]["error"]) > 0);
    }

    /**
     * Checks and corrects the response object depending on the possible kinds of returns.
     *
     * @param array $response An endpoint response array.
     */
    public static function cleanRequestData(array $response): array
    {
        if (isset($response["data"]) && is_string($response["data"])) {
            $response["data"] = json_decode($response["data"], true);
        }
        if (isset($response["code"]) && ($response["code"] < 200 || $response["code"] > 302)) {
            return $response;
        }
        if (isset($response["data"])) {
            if (
                isset($response["data"]["api_request"]["status_code"])
                && (int) ($response["data"]["api_request"]["status_code"]) > 399
            ) {
                $response["code"] = (int) ($response["data"]["api_request"]["status_code"]);
            }
            if (isset($response["data"]["job"]["error"]) && count($response["data"]["job"]["error"]) > 0) {
                $response["code"] = 500;
            }
        }
        return $response;
    }
}
