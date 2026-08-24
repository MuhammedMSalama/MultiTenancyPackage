<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return view('welcome');
        });

        Route::get('/register', [RegisteredUserController::class, 'create'])->name('tenant.create');
        Route::post('/register', [RegisteredUserController::class, 'store'])->name('tenant.create.store');
    });
}
