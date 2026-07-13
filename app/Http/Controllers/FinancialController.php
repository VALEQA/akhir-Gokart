<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FinancialController extends Controller
{
    /**
     * Menampilkan laporan riwayat keuangan admin dengan fitur pencarian
     */
    public function index(Request $request)
    {
        // Proteksi tingkat admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Ambil input pencarian keyword dari form GET
        $keyword = $request->get('search');

        // 1. Query Dasar untuk menghitung total pendapatan bersih (Status Selesai) - Menggunakan TITIK (.)
        $incomeQuery = DB::table('booking')
            ->join('users', 'booking.user_id', '=', 'users.id')
            ->where('booking.status', 'selesai');

        // 2. Query Dasar untuk mengambil riwayat tabel transaksi (Status Selesai) - Menggunakan TITIK (.)
        $historyQuery = DB::table('booking')
            ->join('users', 'booking.user_id', '=', 'users.id')
            ->join('paket_bermain', 'booking.paket_id', '=', 'paket_bermain.id')
            ->select('booking.*', 'users.nama_lengkap', 'users.email', 'paket_bermain.nama_paket')
            ->where('booking.status', 'selesai');

        // Jika ada keyword pencarian, terapkan filter ke kedua query
        if (!empty(trim($keyword))) {
            $incomeQuery->where(function ($q) use ($keyword) {
                $q->where('booking.id', 'LIKE', "%$keyword%")
                  ->orWhere('users.nama_lengkap', 'LIKE', "%$keyword%");
            });

            $historyQuery->where(function ($q) use ($keyword) {
                $q->where('booking.id', 'LIKE', "%$keyword%")
                  ->orWhere('users.nama_lengkap', 'LIKE', "%$keyword%");
            });
        }

        // Eksekusi kalkulasi sum pendapatan bersih
        $total_pendapatan_bersih = $incomeQuery->sum('booking.total_harga');

        // Eksekusi pengambilan daftar transaksi terupdate
        $riwayat_keuangan = $historyQuery->orderBy('booking.created_at', 'desc')->get();

        return view('admin.riwayat_keuangan', compact('riwayat_keuangan', 'total_pendapatan_bersih', 'keyword'));
    }
}