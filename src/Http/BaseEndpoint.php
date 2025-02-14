<?php

namespace Mindee\Http;

use const Mindee\VERSION;

/**
 * Abstract class for endpoints.
 */
abstract class BaseEndpoint
{
    /**
     * @var MindeeApi|MindeeWorkflowApi Settings of the endpoint.
     */
    public $settings;

    /**
     * @param MindeeApi|MindeeWorkflowApi $settings Input settings.
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get the User Agent to send for API calls.
     * @return string
     */
    protected function getUserAgent(): string
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
     * Starts a CURL session, using GET.
     *
     * @param string $queueId ID of the queue to poll.
     * @return array
     */
    protected function initCurlSessionGet(string $queueId): array
    {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: Token ' . $this->settings->apiKey,
            ]
        );

        curl_setopt($ch, CURLOPT_URL, $this->settings->urlRoot . "/documents/queue/$queueId");
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->settings->requestTimeout);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());

        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }

    /**
     * @param resource   $ch         Curl Channel.
     * @param string     $suffix     Optional suffix for the url call.
     * @param array|null $postFields Post fields.
     * @return array
     */
    public function setFinalCurlOpts($ch, string $suffix, ?array $postFields): array
    {
        curl_setopt($ch, CURLOPT_URL, $this->settings->urlRoot . $suffix);
        if ($postFields !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }
}
