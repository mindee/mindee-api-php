<?php

namespace Mindee\error;

use RuntimeException;

class MindeeException extends RuntimeException
{
}

class MindeeMimeTypeException extends MindeeException
{
}

class MindeeClientException extends MindeeException
{
}

class MindeeApiException extends MindeeException
{
}

class MindeeSourceException extends MindeeException
{
}
