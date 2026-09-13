<?php

use App\Actions\Auth\LogoutUserAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard')->block(10, 10);
    Route::livewire('/profile', 'pages::profile')->name('profile')->block(10, 10);
    Route::livewire('/categories', 'pages::categories')->name('categories')->block(10, 10);
    Route::livewire('/plans', 'pages::plans')->name('plans')->block(10, 10);
    Route::livewire('/tasks', 'pages::tasks')->name('tasks')->block(10, 10);
    Route::livewire('/reports', 'pages::reports')->name('reports')->block(10, 10);
    Route::post('/logout', function (LogoutUserAction $logoutUserAction, Request $request) {
        $logoutUserAction->execute($request);
        app()->setLocale(config('app.locale'));
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
});
