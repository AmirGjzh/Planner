<?php

namespace App\Enums;

enum DeleteAccountResult: string
{
    case Success = 'success';
    case WrongPassword = 'wrong_password';
    case RateLimited = 'rate_limited';
}
