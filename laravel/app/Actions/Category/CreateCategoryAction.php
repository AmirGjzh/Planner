<?php

namespace App\Actions\Category;

use App\Enums\CreateCategoryResult;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class CreateCategoryAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(User $user, string $name, Request $request): CreateCategoryResult
    {
        abort_unless($user->can('create', Category::class), 403);

        $name = Str::ucfirst(Str::lower($name));

        $rate_limit_key = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Category creation rate limited.', [
                'user_id' => $user->id,
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return CreateCategoryResult::RateLimited;
        }

        if ($user->categories()->where('name', $name)->exists()) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Category creation failed, already exists.', [
                'user_id' => $user->id,
                'name' => $name,
            ]);

            return CreateCategoryResult::AlreadyExists;
        }

        $user->categories()->create(['name' => $name]);

        RateLimiter::clear($rate_limit_key);

        $this->logger->info('Category created.', [
            'user_id' => $user->id,
            'name' => $name,
        ]);

        return CreateCategoryResult::Created;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('create-category:'.$user->id.'|'.$request->ip());
    }
}
