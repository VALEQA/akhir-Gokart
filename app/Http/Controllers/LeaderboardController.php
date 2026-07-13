<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
public function index(Request $request)
    {
        // GANTI NAMA VARIABEL DARI $view MENJADI $kategori
        $kategori = $request->query('view', 'total_lap');

        // Validasi kolom agar aman
        $allowed_views = ['total_lap', 'sektor_1', 'sektor_2', 'sektor_3'];
        if (!in_array($kategori, $allowed_views)) {
            $kategori = 'total_lap';
        }

        // Judul kolom dinamis
        $sector_titles = [
            'total_lap' => 'Overall Lap',
            'sektor_1'  => 'Sektor 1',
            'sektor_2'  => 'Sektor 2',
            'sektor_3'  => 'Sektor 3'
        ];

        // Ganti $view menjadi $kategori di dalam query
        $leaderboards = DB::table('hasil_balapan as h')
            ->join('users as u', 'h.user_id', '=', 'u.id')
            ->select('u.nama_lengkap', 'u.email', DB::raw("MIN(h.{$kategori}) as waktu_terbaik"))
            ->where("h.{$kategori}", '<', 99.999)
            ->groupBy('h.user_id', 'u.nama_lengkap', 'u.email')
            ->orderBy('waktu_terbaik', 'asc')
            ->get();

        $user_logged = Auth::user();

        // Kirim variabel $kategori (bukan $view) ke Blade
        return view('leaderboard', compact('leaderboards', 'kategori', 'sector_titles', 'user_logged'));
    }
}