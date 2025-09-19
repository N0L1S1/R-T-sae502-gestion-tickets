<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

Route::get('/', fn() => view('welcome'));
Route::get('/dashboard', fn() => view('dashboard'))->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::resource('clients',  ClientController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('tickets',  TicketController::class);
    Route::resource('users', UserController::class);


    Route::post('tickets/{ticket}/assign-me', [TicketController::class, 'assignMe'])
        ->name('tickets.assignMe');

    // désassignation
    Route::post('tickets/{ticket}/unassign-me', [TicketController::class, 'unassignMe'])
        ->name('tickets.unassignMe');

    // routes profil (Breeze)
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
