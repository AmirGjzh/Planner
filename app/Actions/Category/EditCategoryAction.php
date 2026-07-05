<?php

namespace App\Actions\Category;

use App\Enums\EditCategoryResult;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class EditCategoryAction
{
    public function execute(User $user, Category $category, string $name, Request $request): EditCategoryResult
    {
        abort_unless($user->can('update', $category), 403);

        $key = 'edit-category:'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            Log::warning('Category edit rate limited.', [
                'user_id' => $user->id,
                'seconds_remaining' => RateLimiter::availableIn($key),
            ]);

            return EditCategoryResult::RateLimited;
        }

        $existing = $user->categories()
            ->where('name', $name)
            ->where('id', '!=', $category->id)
            ->first();

        if ($existing) {
            RateLimiter::hit($key, 60);
            Log::warning('Category edit failed, already exists.', [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'name' => $name,
            ]);

            return EditCategoryResult::AlreadyExists;
        }

        $category->update(['name' => $name]);

        RateLimiter::hit($key, 60);

        Log::info('Category updated.', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => $name,
        ]);

        return EditCategoryResult::Updated;
    }
}
