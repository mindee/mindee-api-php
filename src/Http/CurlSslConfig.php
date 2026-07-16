<?php

declare(strict_types=1);

namespace Mindee\Http;

use Composer\CaBundle\CaBundle;
use CurlHandle;

/**
 * Configures TLS certificate verification for cURL handles.
 */
class CurlSslConfig
{
    /**
     * Enables peer verification and points cURL at a valid CA bundle.
     *
     * Uses the CA bundle detected by composer/ca-bundle (system store or the
     * bundled Mozilla CA set as a fallback), so verification works reliably
     * across OSes and PHP builds that ship without a configured CA file.
     *
     * @param CurlHandle $ch cURL handle to configure.
     */
    public static function apply(CurlHandle $ch): void
    {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $caPathOrFile = CaBundle::getSystemCaRootBundlePath();
        if (is_dir($caPathOrFile) || (is_link($caPathOrFile) && is_dir((string) readlink($caPathOrFile)))) {
            curl_setopt($ch, CURLOPT_CAPATH, $caPathOrFile);
        } else {
            curl_setopt($ch, CURLOPT_CAINFO, $caPathOrFile);
        }
    }
}
