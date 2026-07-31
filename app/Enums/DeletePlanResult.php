<?php

namespace App\Enums;

enum DeletePlanResult: string
{
    case Deleted = 'deleted';
    case HasTasks = 'has_tasks';
}
