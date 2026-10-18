<?php

namespace App\Actions\Plan;

use App\Enums\EditPlanResult;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final class EditPlanAction
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function execute(User $user, Plan $plan, string $name, ?string $description, array $range, Request $request): EditPlanResult
    {
        abort_unless($user->can('update', $plan), 403);

        // trim() is byte-wise and blind to ZWNJ — strip edge half-spaces with a unicode
        // regex (profile precedent) so a visually identical name can't dodge the unique index.
        $name = Str::ucfirst(Str::lower(preg_replace('/^[\s\x{200C}]+|[\s\x{200C}]+$/u', '', trim($name))));

        $description = $this->nullableString($description);

        $rate_limit_key = $this->rateLimitKey($user, $request);

        if (RateLimiter::tooManyAttempts($rate_limit_key, self::MAX_ATTEMPTS)) {
            $this->logger->warning('Plan edit rate limited.', [
                'user_id' => $user->id,
                'available_in' => RateLimiter::availableIn($rate_limit_key),
            ]);

            return EditPlanResult::RateLimited;
        }

        $existing = $user->plans()
            ->where('name', $name)
            ->where('id', '!=', $plan->id)
            ->first();

        if ($existing) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Plan edit failed, already exists.', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'name' => $name,
            ]);

            return EditPlanResult::AlreadyExists;
        }

        // Editing a range must not strand existing tasks outside it; ISO Y-m-d compares as strings (R2).
        $has_stranded_tasks = $plan->tasks()
            ->where(function ($query) use ($range) {
                $query->where('task_date', '<', $range['start'])
                    ->orWhere('task_date', '>', $range['end']);
            })
            ->exists();

        if ($has_stranded_tasks) {
            RateLimiter::hit($rate_limit_key, self::DECAY_SECONDS);
            $this->logger->warning('Plan edit failed, range excludes tasks.', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'start_date' => $range['start'],
                'finish_date' => $range['end'],
            ]);

            return EditPlanResult::RangeExcludesTasks;
        }

        $plan->update([
            'name' => $name,
            'description' => $description,
            'start_date' => $range['start'],
            'finish_date' => $range['end'],
        ]);

        RateLimiter::clear($rate_limit_key);

        $this->logger->info('Plan updated.', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'name' => $name,
            'start_date' => $range['start'],
            'finish_date' => $range['end'],
        ]);

        return EditPlanResult::Updated;
    }

    private function rateLimitKey(User $user, Request $request): string
    {
        return Str::transliterate('edit-plan:'.$user->id.'|'.$request->ip());
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
