<?php

use App\Actions\Auth\LogoutUserAction;
use App\Actions\Auth\VerifyEmailAction;
use App\Enums\VerifyEmailResult;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard')->block(10, 10);
    Route::livewire('/profile', 'pages::profile')->name('profile')->block(10, 10);
    Route::livewire('/categories', 'pages::categories')->name('categories')->block(10, 10);
    Route::livewire('/plans', 'pages::plans')->name('plans')->block(10, 10);
    Route::livewire('/tasks', 'pages::tasks')->name('tasks')->block(10, 10);
    Route::livewire('/reports', 'pages::reports')->name('reports')->block(10, 10);
});

// The verification notice, the signed verify link, and logout stay outside the
// `verified` gate on purpose: an unverified visitor must be able to read the
// notice and sign out, and the link route verifies through the action itself.
Route::middleware(['auth'])->group(function () {
    Route::livewire('/email/verify', 'pages::auth.verify-email')->name('verification.notice')->block(10, 10);
    // signed:relative binds the signature to path and query, not scheme/host,
    // so the emailed link stays valid across domain or port drift.
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request, VerifyEmailAction $verifyEmailAction) {
        if ($verifyEmailAction->execute($request->user(), $request) === VerifyEmailResult::Verified) {
            session()->flash('toast', [
                'title' => __('Email verified'),
                'variant' => 'success',
                'duration' => 6000,
                'position' => 'bottom-center',
            ]);
        }

        return redirect()->route('dashboard');
    })->middleware(['signed:relative'])->name('verification.verify')->block(10, 10);
    Route::post('/logout', function (LogoutUserAction $logoutUserAction, Request $request) {
        $logoutUserAction->execute($request);
        // No per-user locale runs on this plain HTTP request (only the Livewire
        // auth boundaries set one), so the flash below already lands at the app
        // locale — the toast a guest sees matches the guest page it opens on.
        session()->flash('toast', [
            'title' => __('Logged out successfully'),
            'variant' => 'success',
            'duration' => 6000,
            'position' => 'bottom-center',
        ]);

        return response()->noContent();
    })->name('logout')->block(10, 10);
});

Route::middleware(['guest'])->group(function () {
    Route::livewire('/', 'pages::home')->name('home')->block(10, 10);
    Route::livewire('/login', 'pages::auth.login')->name('login')->block(10, 10);
    Route::livewire('/register', 'pages::auth.register')->name('register')->block(10, 10);
    Route::livewire('/forgot-password', 'pages::auth.forgot-password')->name('password.request')->block(10, 10);
    Route::livewire('/reset-password', 'pages::auth.reset-password')->name('password.reset')->block(10, 10);
});

// Unmatched URLs must still reach the web pipeline: with no matching route,
// Router::findRoute throws before any middleware runs, so the session never
// starts and a signed-in visitor's dead link would render like a guest's
// (app-default theme and locale). Laravel's fallback is GET-only and gets
// partitioned after every real route (never shadowing one), so dead links get
// the same session/auth context as every other page while a POST to a dead
// URL stays a plain framework 404 instead of tripping CSRF verification.
Route::fallback(fn () => abort(404))->block(10, 10);
