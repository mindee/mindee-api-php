<?php

namespace Mindee\Http;

use Mindee\Input\InputSource;
use Mindee\Input\LocalInputSource;
use Mindee\Input\URLInputSource;

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
        curl_setopt($ch, CURLOPT_USERAGENT, 'mindee-api-php@v' . VERSION);

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
        curl_setopt($ch, CURLOPT_USERAGENT, 'mindee-api-php@v' . VERSION);
        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }
}
