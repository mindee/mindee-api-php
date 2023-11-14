<?php

namespace Mindee\error;

use RuntimeException;

class MindeeException extends RuntimeException
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
