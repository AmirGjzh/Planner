<?php

namespace App\Enums;

enum CreatePlanResult: string
{
    case Created = 'created';
    case AlreadyExists = 'already_exists';
    case RateLimited = 'rate_limited';
}
