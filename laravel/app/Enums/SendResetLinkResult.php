<?php

namespace App\Enums;

enum SendResetLinkResult: string
{
    case Sent = 'sent';
    case RateLimited = 'rate_limited';
}
