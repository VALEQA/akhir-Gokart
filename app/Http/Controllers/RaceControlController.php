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

        // Kandidat A: pemilik booking (host)
        $hostBalap = DB::table('booking as b')
            ->join('users as u', 'b.user_id', '=', 'u.id')
            ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
            ->leftJoin('hasil_balapan as h', function ($join) {
                $join->on('b.id', '=', 'h.booking_id')
                     ->on('u.id', '=', 'h.user_id');
            })
            ->select(
                'b.id AS booking_id',
                'b.tanggal_booking',
                'b.jam_booking',
                'u.id AS user_id',
                'u.nama_lengkap',
                'p.nama_paket',
                'h.sektor_1', 'h.sektor_2', 'h.sektor_3', 'h.total_lap'
            )
            ->where('b.status', 'selesai');

        // Kandidat B: peserta open match yang statusnya masih gabung & sudah lunas diverifikasi admin
        $pesertaBalap = DB::table('peserta_open_match as pom')
            ->join('booking as b', 'pom.booking_id', '=', 'b.id')
            ->join('users as u', 'pom.user_id', '=', 'u.id')
            ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
            ->leftJoin('hasil_balapan as h', function ($join) {
                $join->on('b.id', '=', 'h.booking_id')
                     ->on('u.id', '=', 'h.user_id');
            })
            ->select(
                'b.id AS booking_id',
                'b.tanggal_booking',
                'b.jam_booking',
                'u.id AS user_id',
                'u.nama_lengkap',
                'p.nama_paket',
                'h.sektor_1', 'h.sektor_2', 'h.sektor_3', 'h.total_lap'
            )
            ->where('b.status', 'selesai')
            ->where('pom.status', 'bergabung')
            ->where('pom.status_bayar', 'selesai');

        $kandidat_balap = $hostBalap
            ->unionAll($pesertaBalap)
            ->orderBy('booking_id', 'desc')
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
            ['booking_id' => $booking_id, 'user_id' => $user_id],
            [
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