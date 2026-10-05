<?php

namespace App\Enums;

enum VerifyEmailResult: string
{
    case Verified = 'verified';
    case AlreadyVerified = 'already_verified';
}
