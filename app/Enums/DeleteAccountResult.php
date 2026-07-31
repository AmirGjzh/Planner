<?php

namespace App\Enums;

enum DeleteAccountResult: string
{
    case RateLimited = 'rate_limited';
    case WrongPassword = 'wrong_password';
    case Success = 'success';
}
