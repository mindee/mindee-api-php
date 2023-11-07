<?php

namespace Mindee\parsing\common;

class Job
{
}

class AsyncPredictReponse extends ApiResponse
{
    public Job $job;
    public ?Document $document;
}
