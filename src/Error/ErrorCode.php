<?php

namespace Mindee\Error;

/**
 * Enum class holding error codes for exceptions.
 */
class ErrorCode
{
    public const FILE_CANT_PROCESS = 2001;
    public const IMAGE_CANT_PROCESS = 2011;
    public const PDF_CANT_PROCESS = 2021;
    public const PDF_CANT_CREATE = 2022;
    public const PDF_CANT_EDIT = 2021;
    public const FILE_OPERATION_ABORTED = 2030;
    public const FILE_OPERATION_ERROR = 2031;
    public const USER_INPUT_ERROR = 4000;
    public const USER_OPERATION_ERROR = 4001;
    public const GEOMETRIC_OPERATION_FAILED = 5001;
    public const API_REQUEST_FAILED = 5100;
    public const API_TIMEOUT = 5408;
    public const API_UNPROCESSABLE_ENTITY = 5422;
    public const EXCEPTION_BUILD_FAILED = 5200;
    public const INTERNAL_LIBRARY_ERROR = 5999;
}
