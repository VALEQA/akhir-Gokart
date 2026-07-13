<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Menampilkan Daftar Tagihan & Detail Pembayaran Manual
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $selectedId = intval($request->query('id', 0));
        
        $dataBooking = null;

        // 1. QUERY DAFTAR SEMUA TAGIHAN 
        // Bagian A: Booking milik sendiri
        $myOwnBookings = DB::table('booking as b')
            ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
            ->select(
                'b.id', 'b.user_id', 'b.total_harga', 'b.status', 'b.created_at', 
                'b.tanggal_booking', 'b.jam_booking', 'b.jumlah_orang',
                'p.nama_paket', DB::raw('0 as is_partisipan'), DB::raw('p.harga as harga_satuan')
            )
            ->where('b.status', 'aktif')
            ->where('b.user_id', $userId);

        // Bagian B: Booking tempat kita gabung open match
        $allBookings = DB::table('peserta_open_match as pom')
            ->join('booking as b', 'pom.booking_id', '=', 'b.id')
            ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
            ->select(
                'b.id', 'b.user_id', 'b.total_harga', 'b.status', 'b.created_at', 
                'b.tanggal_booking', 'b.jam_booking', 'b.jumlah_orang',
                'p.nama_paket', DB::raw('1 as is_partisipan'), DB::raw('p.harga as harga_satuan')
            )
            ->where('b.status', 'aktif')
            ->where('pom.status', 'bergabung')
            ->where('pom.user_id', $userId)
            ->union($myOwnBookings)
            ->orderBy('created_at', 'desc')
            ->get();

        // Ubah tampilan nominal di list kiri jika statusnya hanya numpang join open match
        foreach ($allBookings as $booking) {
            if ($booking->is_partisipan == 1) {
                $booking->total_harga = $booking->harga_satuan;
            }
        }


        // 2. QUERY DETAIL TAGIHAN YANG SEDANG DIKLIK / DIPILIH
        if ($selectedId > 0) {
            // Cek apakah user menduduki role sebagai partisipan di booking ini
            $cekPartisipan = DB::table('peserta_open_match')
                ->where('booking_id', $selectedId)
                ->where('user_id', $userId)
                ->where('status', 'bergabung')
                ->first();

            if ($cekPartisipan) {
                // JIKA USER ADALAH PESERTA YANG GABUNG MATCH
                $dataBooking = DB::table('booking as b')
                    ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
                    ->join('users as u', 'b.user_id', '=', 'u.id') 
                    ->select(
                        'b.*', 
                        'p.nama_paket', 'p.harga as harga_satuan', 
                        'u.nama_lengkap', 'u.email', 'u.nomor_hp'
                    )
                    ->where('b.id', $selectedId)
                    ->where('b.status', 'aktif')
                    ->first();

                if ($dataBooking) {
                    // Paksa harga menjadi harga satuan gokart
                    $dataBooking->total_harga = intval($dataBooking->harga_satuan); 
                }
            } else {
                // JIKA USER ADALAH PEMILIK UTAMA LAPANGAN (BOOKING MANDIRI)
                $dataBooking = DB::table('booking as b')
                    ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
                    ->join('users as u', 'b.user_id', '=', 'u.id')
                    ->select(
                        'b.*', 
                        'p.nama_paket', 
                        'u.nama_lengkap', 'u.email', 'u.nomor_hp'
                    )
                    ->where('b.id', $selectedId)
                    ->where('b.status', 'aktif')
                    ->where('b.user_id', $userId)
                    ->first();
            }
        }

        // Return ke view cukup membawa allBookings dan dataBooking
        return view('pembayaran', compact('allBookings', 'dataBooking'));
    }

    /**
     * Menyimpan bukti transfer manual (upload gambar/pdf) yang dikirim user.
     * Ini yang tadinya HILANG: tanpa method ini, kolom booking.bukti_transfer
     * tidak pernah terisi, sehingga booking tidak pernah muncul di dashboard admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|integer',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'bukti_transfer.required' => 'Silakan pilih file bukti transfer terlebih dahulu.',
            'bukti_transfer.mimes' => 'File harus berformat JPG, PNG, atau PDF.',
            'bukti_transfer.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $userId = Auth::id();
        $bookingId = $request->input('booking_id');

        $booking = DB::table('booking')
            ->where('id', $bookingId)
            ->where('status', 'aktif')
            ->first();

        if (!$booking) {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Tagihan tidak ditemukan atau sudah diproses.');
        }

        // Pastikan user berhak: pemilik booking ATAU partisipan open match yang sudah bergabung
        $isOwner = $booking->user_id == $userId;
        $isPartisipan = DB::table('peserta_open_match')
            ->where('booking_id', $bookingId)
            ->where('user_id', $userId)
            ->where('status', 'bergabung')
            ->exists();

        if (!$isOwner && !$isPartisipan) {
            abort(403, 'Anda tidak berhak membayar tagihan ini.');
        }

        // PENTING: Di hosting ini, folder Laravel (tempat kode ini berjalan) TERPISAH
        // dari folder yang benar-benar dilayani ke browser. Folder yang benar-benar
        // diakses publik itu ada satu tingkat di ATAS folder Laravel, lalu masuk ke
        // folder "public" di sana (itu sebabnya semua asset lain selalu ditulis
        // asset('public/...') di view). Maka file HARUS ditulis ke lokasi yang sama,
        // bukan ke public_path() bawaan Laravel yang tidak kepanggil dari browser.
        $file = $request->file('bukti_transfer');
        $filename = 'BK' . $bookingId . '_' . time() . '.' . $file->getClientOriginalExtension();

        $realPublicFolder = realpath(base_path('../public'));
        if (!$realPublicFolder) {
            // fallback kalau strukturnya ternyata beda dari dugaan
            $realPublicFolder = public_path();
        }

        $destinationFolder = $realPublicFolder . DIRECTORY_SEPARATOR . 'bukti_transfer';

        if (!file_exists($destinationFolder)) {
            mkdir($destinationFolder, 0755, true);
        }

        $file->move($destinationFolder, $filename);

        // Path yang disimpan TANPA prefix 'public/' (samain sama style qris.jpeg
        // yang juga disimpan tanpa prefix di kode lain, prefix 'public/' baru
        // ditambahkan pas dipanggil pakai asset() di view)
        $path = 'bukti_transfer/' . $filename;

        DB::table('booking')->where('id', $bookingId)->update([
            'bukti_transfer' => $path,
            'updated_at' => now(),
        ]);

        return redirect()->route('pembayaran.index', ['id' => $bookingId])
            ->with('success', 'Bukti pembayaran berhasil dikirim! Menunggu verifikasi admin.');
    }

    /**
     * Webhook Midtrans dinonaktifkan (karena sudah pakai metode manual)
     * Dibiarkan kosong agar route-nya tidak memicu error jika terpanggil.
     */
    public function webhook(Request $request)
    {
        return response()->json(['message' => 'Sistem menggunakan pembayaran manual.'], 200);
    }
}