<?php

namespace App\Enums;

enum CompletePlanResult: string
{
    case HasUndoneTasks = 'has_undone_tasks';

    case Completed = 'completed';
}
