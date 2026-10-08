<?php

namespace App\Enums;

enum ChangePasswordResult: string
{
    case RateLimited = 'rate_limited';
    case WrongCurrentPassword = 'wrong_current_password';
    case SameAsCurrent = 'same_as_current';
    case Success = 'success';
}
