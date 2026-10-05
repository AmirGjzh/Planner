<?php

namespace App\Enums;

enum ResendVerificationResult: string
{
    case Sent = 'sent';
    case RateLimited = 'rate_limited';
    case AlreadyVerified = 'already_verified';
}
