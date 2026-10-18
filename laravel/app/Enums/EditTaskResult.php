<?php

namespace App\Enums;

enum EditTaskResult: string
{
    case Updated = 'updated';
    case RateLimited = 'rate_limited';
    case InvalidCategory = 'invalid_category';
    case InvalidPlan = 'invalid_plan';
    case DateOutOfRange = 'date_out_of_range';
}
