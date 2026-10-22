<?php

namespace App\Actions\Plan;

use App\Enums\CreatePlanResult;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class CreatePlanAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(User $user, string $name, ?string $description, array $range, Request $request): CreatePlanResult
    {
        abort_unless($user->can('create', Plan::class), 403);

        // Str::squish trims the edges (Str::trim's charlist covers ZWNJ — a visually identical
        // name can't dodge the unique index) and collapses internal whitespace runs to one
        // space. No case folding: the casing the user typed is what gets stored (user decision).
        $name = Str::squish($name);

        $description = $this->nullableString($description);

        $rate_limit_key = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Plan creation rate limited.', [
                'user_id' => $user->id,
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return CreatePlanResult::RateLimited;
        }

        if ($user->plans()->where('name', $name)->exists()) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Plan creation failed, already exists.', [
                'user_id' => $user->id,
                'name' => $name,
            ]);

            return CreatePlanResult::AlreadyExists;
        }

        $user->plans()->create([
            'name' => $name,
            'description' => $description,
            'start_date' => $range['start'],
            'finish_date' => $range['end'],
        ]);

        RateLimiter::clear($rate_limit_key);

        $this->logger->info('Plan created.', [
            'user_id' => $user->id,
            'name' => $name,
            'start_date' => $range['start'],
            'finish_date' => $range['end'],
        ]);

        return CreatePlanResult::Created;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('create-plan:'.$user->id.'|'.$request->ip());
    }

    private function nullableString(?string $value): ?string
    {
        // trim() is byte-wise: ZWNJ (e2 80 8c) shares its tail byte with ی (db 8c), so a
        // half-space in the mask would corrupt Persian text. Strip edge half-spaces with a
        // unicode regex instead — a stray leading/trailing نیم‌فاصله is invisible in storage.
        $value = is_string($value) ? preg_replace('/^[\s\x{200C}]+|[\s\x{200C}]+$/u', '', trim($value)) : null;

        return filled($value) ? $value : null;
    }
}
