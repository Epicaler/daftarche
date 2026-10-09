<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\LogoutController;
use App\Livewire;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::livewire('/login', Livewire\Auth\Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::livewire('/', Livewire\Dashboard::class)->name('dashboard');
    Route::livewire('/transactions', Livewire\Transactions\Index::class)->name('transactions');
    Route::livewire('/debts', Livewire\Debts\Index::class)->name('debts');
    Route::livewire('/people', Livewire\People\Index::class)->name('people');
    Route::livewire('/people/{person}', Livewire\People\Show::class)->name('people.show');
    Route::livewire('/categories', Livewire\Categories::class)->name('categories');
    Route::livewire('/reports', Livewire\Reports::class)->name('reports');
    Route::livewire('/settings', Livewire\Settings::class)->name('settings');
    Route::get('/export/{type}', ExportController::class)->name('export');

    Route::post('/logout', LogoutController::class)->name('logout');
});
