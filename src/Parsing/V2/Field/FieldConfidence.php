<?php

namespace Mindee\Parsing\V2\Field;

enum FieldConfidence: string
{
    case Certain = 'Certain';
    case High = 'High';
    case Medium = 'Medium';
    case Low = 'Low';
}
