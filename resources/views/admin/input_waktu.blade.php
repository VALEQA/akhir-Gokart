<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Input Waktu</title>
    <link rel="stylesheet" href="{{ asset('style-admin.css') }}">
    <style>
        .form-waktu { display: flex; gap: 8px; align-items: center; }
        .input-waktu { width: 80px; padding: 0.4rem; border: 1px solid #ddd; border-radius: 6px; text-align: center; font-size: 0.9rem; }
        .btn-simpan { padding: 0.4rem 1rem; background-color: #1d3557; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem; }
        .btn-simpan:hover { background-color: #457b9d; }
        .text-total { font-weight: bold; color: #e63946; font-size: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-top">
                <div class="brand">
                    <h2>GoKart Admin</h2>
                    <p>Management System</p>
                </div>
                <ul class="nav-menu">
                    <li><a href="{{ route('admin.dashboard') }}" class="nav-link"><span>Dashboard</span></a></li>
                    <li><a href="{{ route('admin.riwayat_keuangan') }}" class="nav-link"><span>Riwayat Keuangan</span></a></li>
                    <li><a href="{{ route('admin.input_waktu') }}" class="nav-link active"><span>Input Waktu Balap</span></a></li>
                    <li><a href="{{ route('admin.leaderboard.index') }}" class="nav-link "><span>Lihat Leaderboard</span></a></li>
                    <li><a href="{{ route('admin.kelolapaket') }}" class="nav-link"><span>Kelola Paket</span></a></li>
                    <li><a href="{{ route('admin.kelolausers') }}" class="nav-link"><span>Kelola Users</span></a></li>
                </ul>
            </div>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong>Admin Panel</strong></p>
                    <small>Race Director</small>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <button class="logout-btn" onclick="if(confirm('Keluar dari panel admin?')) document.getElementById('logout-form').submit();">
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Race Control - Input Timing Sensor</h1>
                <p>Masukkan data catatan waktu transponder pembalap per sektor (dalam satuan detik, contoh: 12.345)</p>
            </header>

            @if(session('success'))
                <div class="alert alert-success" style="padding: 1rem; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600;">
                    {!! session('success') !!}
                </div>
            @endif

            <section class="card table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Nama Pembalap</th>
                            <th>Sesi Jadwal</th>
                            <th style="text-align: center;">Input Catatan Waktu Sektor (Detik)</th>
                            <th style="text-align: center;">Best Lap Terhitung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($kandidat_balap) == 0)
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                Belum ada pembalap yang status transaksinya lunas hari ini.
                            </td>
                        </tr>
                        @else
                            @foreach ($kandidat_balap as $row)
                            <tr>
                                <td><strong>#BK-{{ $row->booking_id }}</strong></td>
                                <td>
                                    <strong>{{ $row->nama_lengkap }}</strong><br>
                                    <small style="color: var(--gray);">{{ $row->nama_paket }}</small>
                                </td>
                                <td>
                                    {{ date('d M Y', strtotime($row->tanggal_booking)) }}<br>
                                    <small>Pukul {{ date('H:i', strtotime($row->jam_booking)) }} WIB</small>
                                </td>
                                <td style="text-align: center;">
                                    <form action="{{ route('admin.input_waktu.simpan') }}" method="POST" class="form-waktu">
                                        @csrf
                                        <input type="hidden" name="booking_id" value="{{ $row->booking_id }}">
                                        <input type="hidden" name="user_id" value="{{ $row->user_id }}">
                                        
                                        <input type="number" name="sektor_1" step="0.001" min="0" placeholder="Sektor 1" class="input-waktu" value="{{ $row->sektor_1 != 99.999 && $row->sektor_1 != null ? $row->sektor_1 : '' }}" required>
                                        <input type="number" name="sektor_2" step="0.001" min="0" placeholder="Sektor 2" class="input-waktu" value="{{ $row->sektor_2 != 99.999 && $row->sektor_2 != null ? $row->sektor_2 : '' }}" required>
                                        <input type="number" name="sektor_3" step="0.001" min="0" placeholder="Sektor 3" class="input-waktu" value="{{ $row->sektor_3 != 99.999 && $row->sektor_3 != null ? $row->sektor_3 : '' }}" required>
                                        
                                        <button type="submit" class="btn-simpan">Update</button>
                                    </form>
                                </td>
                                <td style="text-align: center;">
                                    <span class="text-total">
                                        {{ $row->total_lap != 99.999 && $row->total_lap != null ? number_format($row->total_lap, 3) . " s" : "Belum Balap" }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>