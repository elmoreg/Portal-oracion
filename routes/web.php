<?php

use App\Http\Controllers\DashboardRedirectController;
use App\Models\PrayerRequest;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    $publicRequests = PrayerRequest::where('is_public', true)
        ->latest()
        ->take(12)
        ->get();

    return view('welcome', compact('publicRequests'));
})->name('home');

Route::get('dashboard', DashboardRedirectController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('locale/{locale}', function (string $locale) {
    $available = array_keys(config('app.available_locales', [
        'es' => [],
        'en' => [],
        'pt' => [],
        'it' => [],
        'ar' => [],
    ]));

    if (in_array($locale, $available, true)) {
        session(['locale' => $locale]);
    }

    return back(fallback: route('home'));
})->name('locale.switch');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TestimonyController;

// Petición pública, anónima: cualquiera puede crear una y luego
// darle seguimiento con el enlace único que recibe (sin necesidad de cuenta).
Volt::route('muro', 'pages.public.muro')->name('prayer.muro');
Volt::route('muro/orar/{prayerRequest}', 'pages.public.pray')->name('prayer.pray');
Volt::route('peticion/nueva', 'pages.public.new-request')->name('prayer.create');
Volt::route('p/{prayerRequest}', 'pages.public.show')->name('prayer.show');

Route::get('articulos', [ArticleController::class, 'index'])->name('articles.index');
Route::get('articulos/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('testimonios', [TestimonyController::class, 'index'])->name('testimonies.index');
Route::get('testimonios/nuevo', [TestimonyController::class, 'create'])->name('testimonies.create');
Route::post('testimonios', [TestimonyController::class, 'store'])->name('testimonies.store');

Route::middleware(['auth', 'verified', 'role:intercessor'])
    ->prefix('orar')
    ->name('intercessor.')
    ->group(function () {
        Volt::route('/', 'pages.intercessor.dashboard')->name('dashboard');
        Volt::route('peticiones/{prayerRequest:id}', 'pages.intercessor.show')->name('prayer-requests.show');
    });

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Volt::route('/', 'pages.admin.dashboard')->name('dashboard');
        Volt::route('peticiones', 'pages.admin.prayer-requests.index')->name('prayer-requests.index');
        Volt::route('peticiones/{prayerRequest:id}', 'pages.admin.prayer-requests.show')->name('prayer-requests.show');
        Volt::route('intercesores', 'pages.admin.intercessors.index')->name('intercessors.index');
        Volt::route('usuarios', 'pages.admin.users.index')->name('users.index');
    });

require __DIR__.'/auth.php';
