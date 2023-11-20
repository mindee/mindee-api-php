<?php

namespace Mindee\Error;

class MindeeHttpException extends MindeeException
{
    public int $status_code;
    public ?string $api_code;
    public $api_details;
    public ?string $api_message;

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
            $details = "\n".json_encode($this->api_details, JSON_PRETTY_PRINT)."\n";
        } else {
            $details = strval($this->api_details);
        }
        parent::__construct("$url $this->status_code HTTP error: $details - $this->api_message");
    }

    public static function create_error_obj($response): array
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

    public static function handle_error(string $url, array $response, int $code): MindeeHttpException
    {
        $error_obj = MindeeHttpException::create_error_obj($response);
        if ($code >= 400 && $code <= 499) {
            return new MindeeHttpClientException($error_obj, $url, $code);
        }
        if ($code >= 500 && $code <= 599) {
            return new MindeeHttpClientException($error_obj, $url, $code);
        }

        return new MindeeHttpException($error_obj, $url, $code);
    }
}

