<?php

use App\Actions\Auth\LogoutUserAction;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('/profile', 'pages::profile')->name('profile');
    Route::livewire('/category-page', 'pages::category-page')->name('category-page');
    Route::livewire('/plan-page', 'pages::plan-page')->name('plan-page');
    Route::livewire('/task-page', 'pages::task-page')->name('task-page');
    Route::post('/logout', function (LogoutUserAction $logoutUserAction) {
        $logoutUserAction->execute(request());

        return response()->noContent();
    })->name('logout');
});

Route::middleware(['guest'])->group(function () {
    Route::livewire('/', 'pages::home')->name('home');
    Route::livewire('/login', 'pages::auth.login')->name('login');
    Route::livewire('/register', 'pages::auth.register')->name('register');
});
