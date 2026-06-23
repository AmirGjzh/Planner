<?php

namespace App\Enums;

enum RegisterResult: string
{
    case UsernameTaken = 'username_taken';
    case EmailTaken = 'email_taken';
    case Success = 'success';
    case RateLimited = 'rate_limited';
}
