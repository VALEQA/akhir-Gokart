<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RaceControlController extends Controller
{
    /**
     * Menampilkan halaman daftar kandidat pembalap lunas untuk diinput timing transponder
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $kandidat_balap = DB::table('booking')
            ->join('users', 'booking.user_id', '=', 'users.id')
            ->join('paket_bermain', 'booking.paket_id', '=', 'paket_bermain.id')
            ->leftJoin('hasil_balapan', 'booking.id', '=', 'hasil_balapan.booking_id')
            ->select(
                'booking.id AS booking_id', 
                'booking.tanggal_booking', 
                'booking.jam_booking', 
                'users.id AS user_id', 
                'users.nama_lengkap', 
                'paket_bermain.nama_paket', 
                'hasil_balapan.sektor_1', 
                'hasil_balapan.sektor_2', 
                'hasil_balapan.sektor_3', 
                'hasil_balapan.total_lap'
            )
            ->where('booking.status', 'selesai')
            ->orderBy('booking.id', 'desc')
            ->get();

        return view('admin.input_waktu', compact('kandidat_balap'));
    }

    /**
     * Menyimpan atau memperbarui data catatan waktu sensor sektor gokart
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $request->validate([
            'booking_id' => 'required|integer',
            'user_id'    => 'required|integer',
            'sektor_1'   => 'required|numeric|min:0',
            'sektor_2'   => 'required|numeric|min:0',
            'sektor_3'   => 'required|numeric|min:0',
        ]);

        $booking_id = $request->booking_id;
        $user_id    = $request->user_id;
        $sektor_1   = floatval($request->sektor_1);
        $sektor_2   = floatval($request->sektor_2);
        $sektor_3   = floatval($request->sektor_3);
        $total_lap  = $sektor_1 + $sektor_2 + $sektor_3;

        DB::table('hasil_balapan')->updateOrInsert(
            ['booking_id' => $booking_id],
            [
                'user_id'    => $user_id,
                'sektor_1'   => $sektor_1,
                'sektor_2'   => $sektor_2,
                'sektor_3'   => $sektor_3,
                'total_lap'  => $total_lap,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $user = User::findOrFail($user_id);
        $best_time_lama = floatval($user->best_time);

        if ($best_time_lama == 99.999 || $total_lap < $best_time_lama) {
            $user->update(['best_time' => $total_lap]);
        }

        return redirect()->route('admin.input_waktu')->with('success', 'Catatan waktu berhasil disimpan! Total Lap: ' . number_format($total_lap, 3) . ' detik.');
    }
}