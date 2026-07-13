<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Booking #BK{{ str_pad($data->id, 3, '0', STR_PAD_LEFT) }}</title>
    <link rel="stylesheet" href="{{ asset('public/style1.css') }}">
    <link rel="stylesheet" href="{{ asset('public/riwayat_booking.css') }}">
</head>
<body>
<div class="container">
    <main class="main" style="width: 100%; max-width: 800px; margin: 40px auto; padding: 0 20px;">
        <header class="page-header" style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('booking.history') }}" style="padding: 8px 15px; background: #f1faee; border: 1px solid #dee2e6; border-radius: 6px; text-decoration: none; color: #1d3557; font-size: 0.9rem;">
                ⬅️ Kembali
            </a>
            <h1 style="margin: 0; font-size: 1.6rem;">Detail Booking #BK{{ str_pad($data->id, 3, '0', STR_PAD_LEFT) }}</h1>
        </header>

        <div class="info-card">
            <h3>Status Pemesanan</h3>
            <div class="info-row">
                <span class="label">Status Tiket</span>
                <span class="status-badge {{ $status }}" style="padding: 4px 10px; border-radius: 20px; font-weight: bold; font-size: 0.85rem;">{{ ucfirst($data->status) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Waktu Pemesanan</span>
                <span class="value">{{ date('d M Y, H:i', strtotime($data->created_at)) }} WIB</span>
            </div>
        </div>

        <div class="info-card">
            <h3>Detail Jadwal Balapan</h3>
            <div class="info-row">
                <span class="label">Nama Pemesan</span>
                <span class="value">{{ $data->nama_user }}</span>
            </div>
            <div class="info-row">
                <span class="label">Paket Bermain</span>
                <span class="value"><strong>{{ ucfirst($data->nama_paket) }}</strong> ({{ $data->durasi_menit ?? 15 }} Menit)</span>
            </div>
            <div class="info-row">
                <span class="label">Tanggal Bermain</span>
                <span class="value">{{ date('d F Y', strtotime($data->tanggal_booking)) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Jam Sesi</span>
                <span class="value">{{ date('H:i', strtotime($data->jam_booking)) }} WIB</span>
            </div>
            <div class="info-row">
                <span class="label">Jumlah Peserta</span>
                <span class="value">{{ $data->jumlah_orang }} Orang</span>
            </div>
        </div>

        <div class="info-card">
            <h3>Rincian Pembayaran</h3>
            <div class="info-row" style="border-top: 2px dashed #dee2e6; padding-top: 15px; margin-top: 10px; display: flex; justify-content: space-between;">
                <span class="label" style="font-size: 1.1rem; font-weight: 700;">Total Bayar</span>
                <span class="value" style="font-size: 1.2rem; color: #e63946; font-weight: 700;">
                    Rp {{ number_format($data->total_harga, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <div style="margin-top: 25px; display: flex; gap: 15px; justify-content: flex-end;">
            @if($can_cancel)
                <form action="{{ route('booking.cancel', $data->id) }}" method="POST" onsubmit="return confirm('Apakah Anda benar-benar yakin ingin membatalkan booking ini?')">
                    @csrf
                    <button type="submit" style="background-color: #e63946; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                        🚫 Batalkan Booking
                    </button>
                </form>
            @endif

            @if(empty($data->bukti_transfer) && $status === 'aktif')
                <a href="{{ route('pembayaran.index') }}" style="background-color: #1d3557; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                    💳 Selesaikan Pembayaran
                </a>
            @endif
        </div>
    </main>
</div>
</body>
</html>