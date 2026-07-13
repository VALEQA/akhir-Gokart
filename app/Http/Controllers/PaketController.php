<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaketBermain; // Pastikan model PaketBermain di-import
use Illuminate\Support\Facades\Auth;

class PaketController extends Controller
{
    /**
     * Menampilkan halaman Manajemen Harga & Preview Kalkulasi Paket
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Ambil harga saat ini dengan nilai fallback default
        $harga_weekday = 50000;
        $harga_weekend = 60000;

        $weekday_paket = PaketBermain::where('nama_paket', 'weekday')->first();
        $weekend_paket = PaketBermain::where('nama_paket', 'weekend')->first();

        if ($weekday_paket) {
            $harga_weekday = $weekday_paket->harga;
        }
        if ($weekend_paket) {
            $harga_weekend = $weekend_paket->harga;
        }

        return view('admin.kelola_paket', compact('harga_weekday', 'harga_weekend'));
    }

    /**
     * Memproses pembaruan harga Weekday & Weekend (Upsert)
     */
    public function updateHarga(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $request->validate([
            'harga_weekday' => 'required|integer|min:0',
            'harga_weekend' => 'required|integer|min:0',
        ]);

        // Logic Upsert untuk Weekday
        PaketBermain::updateOrCreate(
            ['nama_paket' => 'weekday'],
            [
                'deskripsi' => 'Weekday (Senin–Jumat)',
                'durasi_menit' => 60,
                'maksimal_orang' => 5,
                'harga' => $request->harga_weekday
            ]
        );

        // Logic Upsert untuk Weekend
        PaketBermain::updateOrCreate(
            ['nama_paket' => 'weekend'],
            [
                'deskripsi' => 'Weekend (Sabtu–Minggu)',
                'durasi_menit' => 60,
                'maksimal_orang' => 5,
                'harga' => $request->harga_weekend
            ]
        );

        return redirect()->route('admin.kelolapaket')->with('success', 'Harga bermain berhasil diperbarui!');
    }
}