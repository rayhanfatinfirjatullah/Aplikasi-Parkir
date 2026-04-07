<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAktivitas;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', App\Livewire\Auth\Login::class)->name('login');
});

// Logout
Route::post('/logout', function () {
    LogAktivitas::create([
        'id_user' => auth()->id(),
        'aktivitas' => 'Logout dari sistem',
        'waktu_aktivitas' => now(),
    ]);
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard - all roles
    Route::get('/dashboard', App\Livewire\Dashboard::class)->name('dashboard');

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/users', App\Livewire\Admin\UserManagement::class)->name('admin.users');
        Route::get('/tarif', App\Livewire\Admin\TarifManagement::class)->name('admin.tarif');
        Route::get('/area', App\Livewire\Admin\AreaParkirManagement::class)->name('admin.area');
        Route::get('/kendaraan', App\Livewire\Admin\KendaraanManagement::class)->name('admin.kendaraan');
        Route::get('/log', App\Livewire\Admin\LogAktivitas::class)->name('admin.log');
    });

    // Petugas routes
    Route::middleware('role:petugas')->prefix('petugas')->group(function () {
        Route::get('/transaksi-masuk', App\Livewire\Petugas\TransaksiMasuk::class)->name('petugas.transaksi-masuk');
        Route::get('/transaksi-keluar', App\Livewire\Petugas\TransaksiKeluar::class)->name('petugas.transaksi-keluar');
        Route::get('/cetak-karcis/{id}', App\Livewire\Petugas\CetakKarcis::class)->name('petugas.cetak-karcis');
        Route::get('/cetak-struk/{id}', App\Livewire\Petugas\CetakStruk::class)->name('petugas.cetak-struk');
    });

    // Owner routes
    Route::middleware('role:owner')->prefix('owner')->group(function () {
        Route::get('/laporan', App\Livewire\Owner\LaporanTransaksi::class)->name('owner.laporan');
    });
});
