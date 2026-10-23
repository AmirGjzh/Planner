<?php

namespace App\Enums;

enum ToggleTaskDoneResult: string
{
    case Toggled = 'toggled';
    case Reactivated = 'reactivated';
    case RateLimited = 'rate_limited';
}
