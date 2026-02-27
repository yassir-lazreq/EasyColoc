<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Colocations
    Route::resource('colocations', ColocationController::class);
    Route::post('colocations/{colocation}/leave', [ColocationController::class, 'leave'])->name('colocations.leave');
    Route::post('colocations/{colocation}/transfer-ownership', [ColocationController::class, 'transferOwnership'])->name('colocations.transfer-ownership');
    Route::delete('colocations/{colocation}/members/{user}', [ColocationController::class, 'removeMember'])->name('colocations.members.remove');
    
    // Categories (owner only, nested under colocation)
    Route::resource('colocations.categories', CategoryController::class)->only(['store', 'update', 'destroy']);

    // Expenses (nested under colocation)
    Route::resource('colocations.expenses', ExpenseController::class);
    
    // Payments (nested under colocation)
    Route::resource('colocations.payments', PaymentController::class);
    
    // Balances
    Route::get('colocations/{colocation}/balances', [ColocationController::class, 'balances'])->name('colocations.balances');
    
    // Invitations (sent)
    Route::post('invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/ban', [AdminController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [AdminController::class, 'unban'])->name('users.unban');
    Route::post('/users/{user}/promote', [AdminController::class, 'promote'])->name('users.promote');
    Route::get('/colocations', [AdminController::class, 'colocations'])->name('colocations');
    Route::delete('/colocations/{colocation}', [AdminController::class, 'destroyColocation'])->name('colocations.destroy');
});

// Public invitation routes (no auth)
Route::get('invitations/{token}/accept', [InvitationController::class, 'showAccept'])->name('invitations.show-accept');
Route::post('invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
Route::post('invitations/{token}/refuse', [InvitationController::class, 'refuse'])->name('invitations.refuse');

require __DIR__.'/auth.php';
