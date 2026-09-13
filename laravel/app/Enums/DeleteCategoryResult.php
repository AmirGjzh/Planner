<?php

namespace App\Enums;

enum DeleteCategoryResult: string
{
    case Deleted = 'deleted';
    case HasTasks = 'has_tasks';
}
