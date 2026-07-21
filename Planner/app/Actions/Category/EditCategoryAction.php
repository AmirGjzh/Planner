<?php

namespace App\Actions\Category;

use App\Enums\EditCategoryResult;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

final class EditCategoryAction
{
    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(User $user, Category $category, string $name, Request $request): EditCategoryResult
    {
        abort_unless($user->can('update', $category), 403);

        $rate_limit_key = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            Log::warning('Category edit rate limited.', [
                'user_id' => $user->id,
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return EditCategoryResult::RateLimited;
        }

        $existing = $user->categories()
            ->where('name', $name)
            ->where('id', '!=', $category->id)
            ->first();

        if ($existing) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            Log::warning('Category edit failed, already exists.', [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'name' => $name,
            ]);

            return EditCategoryResult::AlreadyExists;
        }

        $category->update(['name' => $name]);

        RateLimiter::clear($rate_limit_key);

        Log::info('Category updated.', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => $name,
        ]);

        return EditCategoryResult::Updated;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('edit-category:'.$user->id.'|'.$request->ip());
    }
}
