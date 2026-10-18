<?php

namespace App\Enums;

enum EditPlanResult: string
{
    case Updated = 'updated';
    case AlreadyExists = 'already_exists';
    case RateLimited = 'rate_limited';
    case RangeExcludesTasks = 'range_excludes_tasks';
}
