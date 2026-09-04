<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Livewire\Attributes\Computed;

trait HasUser
{
    public function boot()
    {
        app()->setLocale($this->user->locale);
    }

    #[Computed]
    public function user(): User
    {
        return User::query()->findOrFail(auth()->id() ?? abort(403));
    }
}
