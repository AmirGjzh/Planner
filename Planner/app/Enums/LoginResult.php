<?php

namespace App\Enums;

enum LoginResult: string
{
    case Fail = 'fail';
    case Success = 'success';
    case RateLimited = 'rate_limited';
}
