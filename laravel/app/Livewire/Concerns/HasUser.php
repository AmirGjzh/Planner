<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Livewire\Attributes\Computed;

trait HasUser
{
    /**
     * Livewire calls boot() on every request — initial render and each
     * rehydration — so the per-user UI language holds for the whole component
     * lifecycle, not just first paint.
     */
    public function boot()
    {
        app()->setLocale($this->user->locale);
    }

    #[Computed]
    public function user(): User
    {
        return auth()->user() ?? abort(403);
    }
}
