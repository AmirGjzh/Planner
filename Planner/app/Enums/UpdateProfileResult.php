<?php

namespace App\Enums;

enum UpdateProfileResult: string
{
    case Success = 'success';
    case UsernameTaken = 'username_taken';
    case RateLimited = 'rate_limited';
}
