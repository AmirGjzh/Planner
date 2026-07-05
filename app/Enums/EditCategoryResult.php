<?php

namespace App\Enums;

enum EditCategoryResult: string
{
    case Updated = 'updated';
    case AlreadyExists = 'already_exists';
    case RateLimited = 'rate_limited';
}
