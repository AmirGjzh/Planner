<?php

namespace App\Actions\Category;

use App\Enums\CreateCategoryResult;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class CreateCategoryAction
{
    public function execute(User $user, string $name, Request $request): CreateCategoryResult
    {
        abort_unless($user->can('create', Category::class), 403);

        $key = 'create-category:'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            Log::warning('Category creation rate limited.', [
                'user_id' => $user->id,
                'seconds_remaining' => RateLimiter::availableIn($key),
            ]);

            return CreateCategoryResult::RateLimited;
        }

        if ($user->categories()->where('name', $name)->exists()) {
            RateLimiter::hit($key, 60);
            Log::warning('Category creation failed, already exists.', [
                'user_id' => $user->id,
                'name' => $name,
            ]);

            return CreateCategoryResult::AlreadyExists;
        }

        $user->categories()->create([
            'name' => $name,
            'user_id' => $user->id,
        ]);

        RateLimiter::hit($key, 60);

        Log::info('Category created.', [
            'user_id' => $user->id,
            'name' => $name,
        ]);

        return CreateCategoryResult::Created;
    }
}
