<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OpenMatchController extends Controller
{
    /**
     * Menampilkan Halaman Utama Open Match (Migrasi dari open_match.php)
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $filterTanggal = $request->query('tanggal');

        // Query Open Match yang tersedia
        $openMatchesQuery = DB::table('booking as b')
            ->join('users as u', 'b.user_id', '=', 'u.id')
            ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
            ->select(
                'b.*', 'u.nama_lengkap', 'p.nama_paket', 'p.durasi_menit',
                DB::raw("(SELECT COUNT(*) FROM peserta_open_match pom WHERE pom.booking_id = b.id AND pom.status = 'bergabung') as jumlah_peserta")
            )
            ->where('b.is_open_match', 1)
            ->where('b.status', 'aktif')
            ->where('b.user_id', '!=', $userId)
            ->where(function($query) {
                $query->where('b.tanggal_booking', '>', Carbon::today())
                      ->orWhere(function($q) {
                          $q->where('b.tanggal_booking', Carbon::today())
                            ->whereRaw("TIMEDIFF(b.jam_booking, CURTIME()) > '03:00:00'");
                      });
            });

if ($filterTanggal) {
            $openMatchesQuery->where('b.tanggal_booking', $filterTanggal);
        }

        // PERBAIKAN: Ganti havingRaw menjadi whereRaw beserta subquery-nya
        $openMatches = $openMatchesQuery->whereRaw("(SELECT COUNT(*) FROM peserta_open_match pom WHERE pom.booking_id = b.id AND pom.status = 'bergabung') < b.maks_slot_open")
            ->orderBy('b.tanggal_booking', 'asc')
            ->orderBy('b.jam_booking', 'asc')
            ->get();
        // Cek status apakah user login sudah bergabung ke match terkait
        foreach ($openMatches as $match) {
            $match->sudah_join = DB::table('peserta_open_match')
                ->where('booking_id', $match->id)
                ->where('user_id', $userId)
                ->where('status', 'bergabung')
                ->exists();
        }

        // Query partisipasi milik user login
        $partisipasi = DB::table('peserta_open_match as pom')
            ->join('booking as b', 'pom.booking_id', '=', 'b.id')
            ->join('users as u', 'b.user_id', '=', 'u.id')
            ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
            ->select(
                'b.*', 'u.nama_lengkap', 'p.nama_paket', 'p.durasi_menit', 'pom.waktu_bergabung', 'pom.id as partisipasi_id',
                DB::raw("(SELECT COUNT(*) FROM peserta_open_match pom2 WHERE pom2.booking_id = b.id AND pom2.status = 'bergabung') as jumlah_peserta")
            )
            ->where('pom.user_id', $userId)
            ->where('pom.status', 'bergabung')
            ->where('b.status', 'aktif')
            ->orderBy('b.tanggal_booking', 'asc')
            ->orderBy('b.jam_booking', 'asc')
            ->get();

        return view('open_match', compact('openMatches', 'partisipasi', 'filterTanggal'));
    }

    /**
     * Proses Bergabung ke Sesi (Migrasi dari proses_gabung.php)
     */
    public function joinMatch(Request $request)
    {
        $userId = Auth::id();
        $bookingId = intval($request->input('booking_id'));

        if ($bookingId <= 0) {
            return response()->json(['sukses' => false, 'pesan' => 'ID Booking tidak valid.']);
        }

        // Ambil data booking & jumlah slot terisi
        $booking = DB::table('booking as b')
            ->select('b.*', DB::raw("(SELECT COUNT(*) FROM peserta_open_match pom WHERE pom.booking_id = b.id AND pom.status = 'bergabung') as jumlah_peserta"))
            ->where('b.id', $bookingId)
            ->where('b.status', 'aktif')
            ->where('b.is_open_match', 1)
            ->first();

        if (!$booking) {
            return response()->json(['sukses' => false, 'pesan' => 'Booking tidak ditemukan atau sudah tidak tersedia.']);
        }

        if ($booking->user_id == $userId) {
            return response()->json(['sukses' => false, 'pesan' => 'Anda tidak bisa bergabung ke booking sendiri.']);
        }

        // Aturan validasi batas waktu (Carbon)
        $now = Carbon::now();
        $bookingDateTime = Carbon::parse($booking->tanggal_booking . ' ' . $booking->jam_booking);
        $bookingDate = Carbon::parse($booking->tanggal_booking);

        if ($bookingDate->isToday()) {
            $batas = $bookingDateTime->copy()->subHours(3);
            if ($now->greaterThanOrEqualTo($batas)) {
                return response()->json(['sukses' => false, 'pesan' => 'Pendaftaran sudah ditutup (maksimal 3 jam sebelum bermain untuk booking hari ini).']);
            }
        } else {
            $batas = $bookingDate->copy()->subDay()->endOfDay();
            if ($now->greaterThan($batas)) {
                return response()->json(['sukses' => false, 'pesan' => 'Pendaftaran sudah ditutup (maksimal H-1 sebelum hari bermain).']);
            }
        }

        // Cek sisa slot kapasitas
        if ($booking->jumlah_peserta >= $booking->maks_slot_open) {
            return response()->json(['sukses' => false, 'pesan' => 'Slot sudah penuh.']);
        }

        // Cek duplikasi pendaftaran
        $sudahJoin = DB::table('peserta_open_match')
            ->where('booking_id', $bookingId)
            ->where('user_id', $userId)
            ->where('status', 'bergabung')
            ->exists();

        if ($sudahJoin) {
            return response()->json(['sukses' => false, 'pesan' => 'Anda sudah bergabung di sesi ini.']);
        }

        // Simpan pendaftaran baru
        $insert = DB::table('peserta_open_match')->insert([
            'booking_id' => $bookingId,
            'user_id' => $userId,
            'status' => 'bergabung',
            'waktu_bergabung' => Carbon::now()
        ]);

        if ($insert) {
            return response()->json([
                'sukses' => true, 
                'pesan' => 'Berhasil bergabung!',
                // Mengarahkan user ke halaman pembayaran membawa ID booking terkait 🚀
                'redirect_url' => route('pembayaran.index', ['id' => $bookingId]) 
            ]);
        } else {
            return response()->json(['sukses' => false, 'pesan' => 'Gagal menyimpan data.']);
        }
    }

    /**
     * Proses Membatalkan Sesi (Migrasi dari proses_batal.php)
     */
    public function leaveMatch(Request $request)
    {
        $userId = Auth::id();
        $partisipasiId = intval($request->input('partisipasi_id'));

        if ($partisipasiId <= 0) {
            return response()->json(['sukses' => false, 'pesan' => 'ID Partisipasi tidak valid.']);
        }

        // Pastikan record partisipasi tersebut valid & milik user terkait
        $partisipasiExists = DB::table('peserta_open_match')
            ->where('id', $partisipasiId)
            ->where('user_id', $userId)
            ->where('status', 'bergabung')
            ->exists();

        if (!$partisipasiExists) {
            return response()->json(['sukses' => false, 'pesan' => 'Data partisipasi tidak ditemukan.']);
        }

        // Update status menjadi 'dibatalkan'
        $update = DB::table('peserta_open_match')
            ->where('id', $partisipasiId)
            ->where('user_id', $userId)
            ->update([
                'status' => 'dibatalkan'
            ]);

        if ($update) {
            return response()->json(['sukses' => true, 'pesan' => 'Partisipasi berhasil dibatalkan.']);
        } else {
            return response()->json(['sukses' => false, 'pesan' => 'Gagal membatalkan partisipasi.']);
        }
    }
}