<?php

namespace App\Enums;

enum ToggleTaskDoneResult: string
{
    case Toggled = 'toggled';
    case RateLimited = 'rate_limited';
}
