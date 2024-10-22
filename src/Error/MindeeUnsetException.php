<?php

/**
 * @file
 * Mindee API Exceptions.
 */

namespace Mindee\Error;

/**
 *  Exceptions relating to products containing unset fields.
 */
class MindeeUnsetException extends MindeeException
{
    /**
     * @var integer Error code falls back to unprocessable entity.
     */
    protected $code = ErrorCode::API_UNPROCESSABLE_ENTITY;
    /**
     * @var string Message is the same for all erroneous fields.
     */
    protected $message = "Improper field value in response.";
}
