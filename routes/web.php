<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OpenMatchController; 
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\HasilBalapanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\RaceControlController;
use App\Http\Controllers\AdminLeaderboardController;

// Rute Publik (Akses Tanpa Login)
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/login', [AuthController::class, 'tampilLogin'])->name('login');
Route::post('/login', [AuthController::class, 'prosesLogin']);
Route::get('/register', [AuthController::class, 'tampilRegister'])->name('register');
Route::post('/register', [AuthController::class, 'prosesRegister']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Rute Terproteksi (Wajib Login)
Route::middleware(['auth'])->group(function () {
    Route::get('/pembayaran', [PaymentController::class, 'index'])->name('pembayaran.index');
    // Fallback: kalau ada yang akses /pembayaran/upload via GET (refresh, bookmark,
    // koneksi putus saat upload, dsb), arahkan balik ke halaman tagihan, bukan 405 mentah.
    Route::get('/pembayaran/upload', function () {
        return redirect()->route('pembayaran.index')
            ->with('error', 'Sesi upload tidak valid, silakan coba lagi.');
    });
    Route::post('/pembayaran/upload', [PaymentController::class, 'store'])->name('pembayaran.store')->middleware('auth');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/booking', [BookingController::class, 'index'])->name('booking');
    Route::get('/booking/paket-id', [BookingController::class, 'getPaketId'])->name('booking.paket_id');
    Route::post('/booking', [BookingController::class, 'prosesBooking'])->name('booking.proses');
    
    Route::get('/booking/riwayat', [BookingController::class, 'riwayat'])->name('booking.history');
    Route::get('/booking/detail/{id}', [BookingController::class, 'detail'])->name('booking.detail');
    Route::post('/booking/batalkan/{id}', [BookingController::class, 'batalkan'])->name('booking.cancel');

    Route::get('/open-match', [OpenMatchController::class, 'index'])->name('open-match.index');
    Route::post('/open-match/join', [OpenMatchController::class, 'joinMatch'])->name('open-match.join');
    Route::post('/open-match/leave', [OpenMatchController::class, 'leaveMatch'])->name('open-match.leave');
    
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    
    Route::get('/hasil-balapan', [HasilBalapanController::class, 'index'])->name('hasil-balapan.index');
    
    Route::get('/profil', [ProfileController::class, 'index'])->name('profil.index');
    Route::post('/profil/update', [ProfileController::class, 'update'])->name('profil.update');
    Route::post('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');
    Route::post('/profil/foto/hapus', [ProfileController::class, 'deletePhoto'])->name('profil.photo.delete');
    
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard/proses/{action}/{id}', [AdminController::class, 'prosesAksi'])->name('admin.dashboard.proses');
    Route::get('/admin/dashboard/proses-peserta/{action}/{id}', [AdminController::class, 'prosesAksiPeserta'])->name('admin.dashboard.proses_peserta');

    Route::get('/admin/paket', [PaketController::class, 'index'])->name('admin.kelolapaket');
    Route::get('/admin/paket/edit/{id}', [PaketController::class, 'edit'])->name('admin.paket.edit');
    Route::post('/admin/paket/update/{id}', [PaketController::class, 'update'])->name('admin.paket.update');

    Route::get('/admin/paket', [PaketController::class, 'index'])->name('admin.kelolapaket');
    Route::post('/admin/paket/update-harga', [PaketController::class, 'updateHarga'])->name('admin.paket.update_harga');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.kelolausers');
    Route::get('/admin/users/edit/{id}', [UserController::class, 'edit'])->name('admin.kelolausers.edit');
    Route::put('/admin/users/update/{id}', [UserController::class, 'update'])->name('admin.kelolausers.update');
    Route::delete('/admin/users/delete/{id}', [UserController::class, 'destroy'])->name('admin.kelolausers.delete');

    Route::get('/admin/keuangan', [FinancialController::class, 'index'])->name('admin.riwayat_keuangan');

    Route::get('/admin/input-waktu', [RaceControlController::class, 'index'])->name('admin.input_waktu');
    Route::post('/admin/input-waktu/simpan', [RaceControlController::class, 'store'])->name('admin.input_waktu.simpan');
    
    Route::get('/admin/leaderboard', [AdminLeaderboardController::class, 'index'])->name('admin.leaderboard.index');
});