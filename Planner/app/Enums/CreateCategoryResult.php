<?php

namespace App\Enums;

enum CreateCategoryResult: string
{
    case Created = 'created';
    case AlreadyExists = 'already_exists';
    case RateLimited = 'rate_limited';
}
