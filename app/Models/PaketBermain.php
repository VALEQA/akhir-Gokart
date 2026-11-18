<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketBermain extends Model
{
    // 1. Kunci nama tabel agar Laravel membaca 'paket_bermain', bukan 'paket_bermains'
    protected $table = 'paket_bermain';

    // 2. Tentukan kolom yang boleh diisi (Mass Assignment) sesuai struktur database kamu
    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'durasi_menit',
        'maksimal_orang',
        'harga'
    ];
}