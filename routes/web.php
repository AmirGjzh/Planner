<?php

use App\Actions\Auth\LogoutUserAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('/profile', 'pages::profile')->name('profile');
    Route::livewire('/categories', 'pages::categories')->name('categories');
    Route::livewire('/plans', 'pages::plans')->name('plans');
    Route::livewire('/tasks', 'pages::tasks')->name('tasks');
    Route::livewire('/reports', 'pages::report-page')->name('report-page');
    Route::post('/logout', function (LogoutUserAction $logoutUserAction, Request $request) {
        $logoutUserAction->execute($request);

        return response()->noContent();
    })->name('logout');
});

Route::middleware(['guest'])->group(function () {
    Route::livewire('/', 'pages::home')->name('home');
    Route::livewire('/login', 'pages::auth.login')->name('login');
    Route::livewire('/register', 'pages::auth.register')->name('register');
});
