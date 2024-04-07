<?php

namespace App\Domain\ValueObject;

enum ReportFormat: string
{
    case HTML = 'html';
    case JSON = 'json';
}
