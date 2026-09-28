<?php

namespace App\Enums;

enum ResetPasswordResult: string
{
    case Reset = 'reset';
    case InvalidLink = 'invalid_link';
    case SameAsCurrent = 'same_as_current';
    case RateLimited = 'rate_limited';
}
