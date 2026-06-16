<?php

namespace App\Models;

use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'title',
    'description',
    'task_date',
    'estimated_minutes',
    'priority',
    'done',
    'day_before_alarm',
    'user_id',
    'plan_id',
    'category_id',
])]
class Task extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'task_date' => 'date:Y-m-d',
            'estimated_minutes' => 'integer',
            'priority' => TaskPriority::class,
            'done' => 'boolean',
            'day_before_alarm' => 'integer',
        ];
    }
}
