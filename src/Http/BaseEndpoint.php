<?php

namespace Mindee\Http;

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
        curl_setopt($ch, CURLOPT_USERAGENT, getUserAgent());

        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }

    /**
     * @param resource    $ch         Curl Channel.
     * @param string      $suffix     Optional suffix for the url call.
     * @param array|null  $postFields Post fields.
     * @param string|null $workflowId Optional ID of the workflow.
     * @return array
     */
    public function setFinalCurlOpts(
        $ch,
        string $suffix,
        ?array $postFields,
        ?string $workflowId = null
    ): array {
        if (isset($workflowId)) {
            $url = $this->settings->baseUrl . "/workflows/" . $workflowId . $suffix;
        } else {
            $url = $this->settings->urlRoot . $suffix;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($postFields !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, getUserAgent());
        $resp = [
            'data' => curl_exec($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        ];
        curl_close($ch);

        return $resp;
    }
}
