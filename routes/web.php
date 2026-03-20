<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/map', 'pages.location-map')->name('location-map');

Route::get('/time-test', function () {
    echo "Szerver (PHP) ideje: " . date('Y-m-d H:i:s') . "<br>";
    echo "Laravel (Carbon) ideje: " . \Carbon\Carbon::now()->toDateTimeString() . "<br>";
    echo "Beállított időzóna: " . config('app.timezone') . "<br>";
    echo "PHP default timezone: " . date_default_timezone_get();
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
