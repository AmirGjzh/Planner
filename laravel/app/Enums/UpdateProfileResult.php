<?php

namespace App\Enums;

enum UpdateProfileResult: string
{
    case RateLimited = 'rate_limited';
    case UsernameTaken = 'username_taken';
    case Success = 'success';
}
