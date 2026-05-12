<?php

declare(strict_types=1);

namespace Mindee\Error;

/**
 * Exceptions relating to client-side HTTP calls.
 *
 * Handles error 400 to 499.
 */
class MindeeHttpClientException extends MindeeHttpException {}
