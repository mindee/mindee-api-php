<?php

/**
 * @file
 * Mindee HTTP Exceptions.
 */

namespace Mindee\Error;

/**
 * Exceptions relating to HTTP calls.
 *
 * Handles uncaught error codes.
 */
class MindeeHttpException extends MindeeException
{
    /**
     * @var integer Status code as sent by the server.
     */
    public int $status_code;
    /**
     * @var string|mixed|null API code as sent by the server.
     */
    public ?string $api_code;
    /**
     * @var mixed|null API details field as sent by the server.
     */
    public $api_details;
    /**
     * @var string|mixed|null API message field as sent by the server.
     */
    public ?string $api_message;

    /**
     * @param array   $http_error Array containing the error data.
     * @param string  $url        Remote URL the error was found on.
     * @param integer $code       Error code.
     */
    public function __construct(array $http_error, string $url, int $code)
    {
        $this->status_code = $code;
        if (array_key_exists('code', $http_error)) {
            $this->api_code = $http_error['code'];
        } else {
            $this->api_code = null;
        }
        if (array_key_exists('details', $http_error)) {
            $this->api_details = $http_error['details'];
        } else {
            $this->api_details = null;
        }
        if (array_key_exists('message', $http_error)) {
            $this->api_message = $http_error['message'];
        } else {
            $this->api_message = null;
        }
        if (is_array($this->api_details)) {
            $details = "\n" . json_encode($this->api_details, JSON_PRETTY_PRINT) . "\n";
        } else {
            $details = strval($this->api_details);
        }
        parent::__construct("$url $this->status_code HTTP error: $details - $this->api_message");
    }

    /**
     * @param $response array|string Parsed server response
     * @return string[]
     */
    public static function createErrorObj($response): array
    {
        if (is_string($response)) {
            if (str_contains($response, 'Maximum pdf pages')) {
                $error_array = [
                    'code' => 'TooManyPages',
                    'message' => 'Maximum amount of pdf pages reached.',
                    'details' => $response,
                ];
            } elseif (str_contains($response, 'Max file size is')) {
                $error_array = [
                    'code' => 'FileTooLarge',
                    'message' => 'Maximum file size reached.',
                    'details' => $response,
                ];
            } elseif (str_contains($response, 'Invalid file type')) {
                $error_array = [
                    'code' => 'InvalidFiletype',
                    'message' => 'Invalid file type.',
                    'details' => $response,
                ];
            } elseif (str_contains($response, 'Gateway timeout')) {
                $error_array = [
                    'code' => 'RequestTimeout',
                    'message' => 'Request timed out.',
                    'details' => $response,
                ];
            } elseif (str_contains($response, 'Too Many Requests')) {
                $error_array = [
                    'code' => 'TooManyRequests',
                    'message' => 'Too Many Requests.',
                    'details' => $response,
                ];
            } else {
                $error_array = [
                    'code' => 'UnknownError',
                    'message' => 'Server sent back an unexpected reply.',
                    'details' => $response,
                ];
            }

            return $error_array;
        }
        if (array_key_exists('api_request', $response) && array_key_exists('error', $response['api_request'])) {
            return $response['api_request']['error'];
        }
        throw new MindeeException('Could not build a specific HTTP exception from: ' . json_encode($response, JSON_PRETTY_PRINT));
    }

    /**
     * @param string  $url      Remote URL the error was found on.
     * @param array   $response Raw server response.
     * @param integer $code     Error code.
     * @return MindeeHttpException
     */
    public static function handleError(string $url, array $response, int $code): MindeeHttpException
    {
        $error_obj = MindeeHttpException::createErrorObj($response);
        if ($code >= 400 && $code <= 499) {
            return new MindeeHttpClientException($error_obj, $url, $code);
        }
        if ($code >= 500 && $code <= 599) {
            return new MindeeHttpClientException($error_obj, $url, $code);
        }

        return new MindeeHttpException($error_obj, $url, $code);
    }
}
