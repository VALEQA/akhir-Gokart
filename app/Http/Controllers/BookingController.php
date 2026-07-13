<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // Halaman Form Booking (Sudah ada)
    public function index()
    {
        $user_logged = Auth::user();

        $harga_weekday = 50000;
        $harga_weekend = 60000;

        $r = DB::table('paket_bermain')
            ->whereIn('nama_paket', ['weekday', 'weekend'])
            ->get();

        foreach ($r as $row) {
            if ($row->nama_paket === 'weekday') {
                $harga_weekday = $row->harga;
            }
            if ($row->nama_paket === 'weekend') {
                $harga_weekend = $row->harga;
            }
        }

        return view('booking', compact('user_logged', 'harga_weekday', 'harga_weekend'));
    }

    // Mengambil ID Paket lewat AJAX (Sudah ada)
    public function getPaketId(Request $request)
    {
        $type = $request->query('type');
        $paket = DB::table('paket_bermain')->where('nama_paket', $type)->first();
        return response()->json($paket ? $paket->id : 0);
    }

    // Proses Simpan Booking Baru (Sudah ada)
    public function prosesBooking(Request $request)
    {
        $user_id = Auth::id();

        $id = DB::table('booking')->insertGetId([
            'user_id' => $user_id,
            'paket_id' => $request->input('paket_id'),
            'tanggal_booking' => $request->input('tanggal_booking'),
            'jam_booking' => $request->input('jam_booking'),
            'jumlah_orang' => $request->input('jumlah_orang'),
            'total_harga' => $request->input('total_harga'),
            'is_open_match' => $request->input('is_open_match'),
            'maks_slot_open' => $request->input('maks_slot_open'),
            'catatan_open_match' => $request->input('catatan_open_match'),
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json($id);
    }

    // =========================================================================
    // KODE TAMBAHAN (MIGRASI DARI FILE RIWAYAT & DETAIL YANG KAMU UNGGAH)
    // =========================================================================

    // Pengganti riwayat_booking.php
    public function riwayat()
    {
        $user_id = Auth::id();
        $user_logged = Auth::user();

        // Menggunakan tanda petik pada 'p.id' agar tidak memicu error undefined constant
        $riwayat = DB::table('booking as b')
            ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
            ->select('b.*', 'p.nama_paket')
            ->where('b.user_id', $user_id)
            ->orderBy('b.tanggal_booking', 'DESC')
            ->orderBy('b.jam_booking', 'DESC')
            ->get();

        // Ganti dari view('booking.riwayat') menjadi:
        return view('riwayat', compact('riwayat', 'user_logged'));
    }

    // Pengganti detail_booking.php
    public function detail($id)
    {
        $user_id = Auth::id();

        // Menggunakan tanda petik pada 'p.id' dan 'u.id'
        $data = DB::table('booking as b')
            ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
            ->join('users as u', 'b.user_id', '=', 'u.id')
            ->select('b.*', 'p.nama_paket', 'p.durasi_menit', 'p.harga as harga_paket', 'u.nama_lengkap as nama_user')
            ->where('b.id', $id)
            ->where('b.user_id', $user_id)
            ->first();

        if (!$data) {
            return redirect()->route('booking.history');
        }

        $status = strtolower($data->status);
        $can_cancel = ($status === 'aktif' && empty($data->bukti_transfer));

        // Ganti dari view('booking.detail') menjadi:
        return view('detail_booking', compact('data', 'status', 'can_cancel'));
    }

    // Pengganti batal_riwayat.php (Aksi Proses Pembatalan)
    public function batalkan($id)
    {
        $user_id = Auth::id();

        // Validasi kepemilikan booking
        $booking = DB::table('booking')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$booking) {
            return redirect()->route('booking.history')->with('msg', 'notfound');
        }

        // Cek syarat pembatalan
        if ($booking->status !== 'aktif' || !empty($booking->bukti_transfer)) {
            return redirect()->route('booking.history')->with('msg', 'cannot_cancel');
        }

        // Jalankan update status pembatalan
        $update = DB::table('booking')
            ->where('id', $id)
            ->update([
                'status' => 'dibatalkan',
                'updated_at' => now()
            ]);

        if ($update) {
            return redirect()->route('booking.history')->with('msg', 'cancel_success');
        } else {
            return redirect()->route('booking.history')->with('msg', 'cancel_failed');
        }
    }
}