<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\WishlistPrintController;

Route::get('/', function () {
    // return view('welcome');
    return Redirect::to('/admin/login');
})->name('home');

Route::get('/view', function () {
    return view(
        'pdf',
        [
            "array" => [
                "id" => 1,
                "user_id" => 1,
                "name" => "eko",
                "description" => "k",
                "created_at" => now(),
                "updated_at" => now(),
                "type" => "wants",
                "sort" => 2,
                "image" => "01JVXV9P9YY8HSSEJ3H3QPWRR2.png",
                "achieved" => false
            ]
        ]
    );
})->name('AAAAA PDF SUCKS');




Route::get('wishlist/{wishlist}/print', [WishlistPrintController::class, 'print'])->name('wishlist.print');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';
