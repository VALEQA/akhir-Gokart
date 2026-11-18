<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    // 1. Beritahu Laravel nama tabel aslinya (tanpa 's')
    protected $table = 'booking';

    // 2. Izinkan kolom ini diisi secara otomatis lewat $booking->update()
    protected $fillable = ['status', 'bukti_transfer'];

    // 3. Relasi ke tabel Users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 4. Relasi ke tabel Paket Bermain
    public function paket()
    {
        return $this->belongsTo(PaketBermain::class, 'paket_id');
    }

    // 5. Trik ajaib: Membuat seolah-olah kolom 'total_harga' ada, 
    // dengan mengambil harga dari relasi paket_bermain (agar view Blade tidak error)
    public function getTotalHargaAttribute()
    {
        return $this->paket ? $this->paket->harga : 0;
    }
}