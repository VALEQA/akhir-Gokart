<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminLeaderboardController extends Controller
{
    /**
     * Menampilkan rekor log lengkap sirkuit di Panel Admin
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $leaderboard = DB::table('hasil_balapan as h')
            ->join('users as u', 'h.user_id', '=', 'u.id')
            ->join('booking as b', 'h.booking_id', '=', 'b.id')
            ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
            ->select('h.*', 'u.nama_lengkap', 'p.nama_paket', 'b.tanggal_booking')
            ->where('h.total_lap', '<', 99.999)
            ->orderBy('h.total_lap', 'asc')
            ->get();

        return view('admin.leaderboard', compact('leaderboard'));
    }
}