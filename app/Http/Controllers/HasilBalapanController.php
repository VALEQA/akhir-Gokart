<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HasilBalapanController extends Controller
{
    public function index()
    {
        $user_logged = Auth::user();
        $user_id = $user_logged->id;

        $hasil_balapan = DB::table('hasil_balapan as h')
            ->join('booking as b', 'h.booking_id', '=', 'b.id')
            ->join('paket_bermain as p', 'b.paket_id', '=', 'p.id')
            ->select('h.*', 'b.tanggal_booking', 'b.jam_booking', 'p.nama_paket')
            ->where('h.user_id', $user_id)
            ->orderBy('b.tanggal_booking', 'desc')
            ->orderBy('b.jam_booking', 'desc')
            ->get();

        // QUERY GRAFIK: Ambil 5 balapan terbaru
        $chart_query = DB::table('hasil_balapan as h')
            ->join('booking as b', 'h.booking_id', '=', 'b.id')
            ->select('h.total_lap', 'b.tanggal_booking')
            ->where('h.user_id', $user_id)
            ->orderBy('b.tanggal_booking', 'desc')
            ->orderBy('b.jam_booking', 'desc')
            ->limit(5)
            ->get();

        $chart_labels = [];
        $chart_data = [];
        
        foreach ($chart_query as $row) {
            $chart_labels[] = date('d M', strtotime($row->tanggal_booking));
            $chart_data[] = (float) $row->total_lap;
        }

        // Balikkan urutan data agar grafik bergerak dari kiri (lama) ke kanan (baru)
        $chart_labels = array_reverse($chart_labels);
        $chart_data = array_reverse($chart_data);

        return view('hasil_balapan', compact('user_logged', 'hasil_balapan', 'chart_labels', 'chart_data'));
    }
}