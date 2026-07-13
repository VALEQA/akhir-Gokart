<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingHistoryController extends Controller
{
    // Menggantikan logika dari riwayat_booking.php
    public function index()
    {
        $riwayatBooking = Booking::with('paketBermain')
            ->where('user_id', Auth::id()) // Memastikan hanya mengambil data user yang login
            ->orderBy('tanggal_booking', 'desc')
            ->orderBy('jam_booking', 'desc')
            ->get();

        return view('booking.riwayat', compact('riwayatBooking'));
    }

    // Menggantikan logika dari detail_booking.php
    public function show($id)
    {
        $booking = Booking::with(['paketBermain', 'user'])
            ->where('id', $id)
            ->where('user_id', Auth::id()) // Proteksi agar user tidak bisa mengintip booking orang lain
            ->firstOrFail(); // Otomatis return halaman 404 jika tidak ditemukan

        return view('booking.detail', compact('booking'));
    }

    // Menggantikan logika dari batal_riwayat.php
    public function cancel($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$booking) {
            return redirect()->route('booking.history')->with('warning', 'Data booking tidak ditemukan.');
        }

        // Hanya boleh dibatalkan jika statusnya 'aktif' dan belum ada bukti transfer
        if ($booking->status !== 'aktif' || !empty($booking->bukti_transfer)) {
            return redirect()->route('booking.history')->with('warning', 'Booking tidak dapat dibatalkan.');
        }

        $booking->update(['status' => 'dibatalkan']);

        return redirect()->route('booking.history')->with('success', 'Booking berhasil dibatalkan.');
    }
}
