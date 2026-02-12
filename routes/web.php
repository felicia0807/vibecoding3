<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome'); // or Inertia render
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
            return view('dashboard'); // or Inertia::render('Dashboard');
        }
        )->name('dashboard');
    });

require __DIR__ . '/auth.php';