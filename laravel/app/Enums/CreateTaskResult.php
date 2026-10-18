<?php

namespace App\Enums;

enum CreateTaskResult: string
{
    case Created = 'created';
    case RateLimited = 'rate_limited';
    case InvalidCategory = 'invalid_category';
    case InvalidPlan = 'invalid_plan';
    case DateOutOfRange = 'date_out_of_range';
}
