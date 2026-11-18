<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom pembayaran untuk partisipan Open Match.
 *
 * Kode aplikasi (PaymentController, AdminController, BookingController,
 * RaceControlController) membaca kolom-kolom ini, tapi sebelumnya hanya ada di
 * database hosting dan belum pernah dibuatkan migration-nya. Tanpa migration ini
 * `php artisan migrate` pada database baru akan menghasilkan tabel yang tidak
 * lengkap dan halaman Open Match / Pembayaran / Dashboard Admin akan error.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_open_match', function (Blueprint $table) {
            if (!Schema::hasColumn('peserta_open_match', 'status_bayar')) {
                // aktif = belum/tidak lunas, selesai = sudah diverifikasi admin
                $table->string('status_bayar', 20)->default('aktif')->after('status');
            }
            if (!Schema::hasColumn('peserta_open_match', 'bukti_transfer')) {
                $table->string('bukti_transfer', 255)->nullable()->after('status_bayar');
            }
            if (!Schema::hasColumn('peserta_open_match', 'waktu_bergabung')) {
                $table->timestamp('waktu_bergabung')->nullable()->after('bukti_transfer');
            }
        });
    }

    public function down(): void
    {
        Schema::table('peserta_open_match', function (Blueprint $table) {
            $table->dropColumn(['status_bayar', 'bukti_transfer', 'waktu_bergabung']);
        });
    }
};
