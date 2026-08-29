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
    Route::livewire('/reports', 'pages::reports')->name('reports');
    Route::post('/logout', function (LogoutUserAction $logoutUserAction, Request $request) {
        $logoutUserAction->execute($request);
        session()->flash('toast', [
            'title' => __('Logged out successfully'),
            'variant' => 'success',
            'duration' => 6000,
            'position' => 'bottom-center',
        ]);

        return response()->noContent();
    })->name('logout');
});

Route::middleware(['guest'])->group(function () {
    Route::livewire('/', 'pages::home')->name('home');
    Route::livewire('/login', 'pages::auth.login')->name('login');
    Route::livewire('/register', 'pages::auth.register')->name('register');
});
