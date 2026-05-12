<?php

declare(strict_types=1);

/**
 * @file
 * Mindee HTTP Server Exceptions.
 */

namespace Mindee\Error;

/**
 * Exceptions relating to server-side HTTP calls.
 *
 * Handles error 500 to 599.
 */
class MindeeHttpServerException extends MindeeHttpException {}
