<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Kelola Paket</title>
    <link rel="stylesheet" href="{{ asset('public/style-admin.css') }}">
    <style>
        .price-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1.5rem; }
        @media (max-width: 768px) {
            .price-grid { grid-template-columns: 1fr; }
        }
        .price-card { background: var(--white); border-radius: 12px; padding: 2rem; box-shadow: var(--shadow); text-align: center; border-top: 4px solid var(--primary); }
        .price-card.weekend-card { border-top-color: #f59e0b; }
        .price-card h3 { font-size: 1.1rem; color: var(--dark); margin-bottom: 0.3rem; }
        .price-card small { color: var(--gray); font-size: 0.85rem; display: block; margin-bottom: 1.2rem; }
        .price-input-wrap { position: relative; }
        .price-input-wrap span { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--gray); font-weight: 600; font-size: 0.9rem; }
        .price-input { width: 100%; padding: 0.7rem 0.7rem 0.7rem 2.5rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1.1rem; font-weight: 700; text-align: right; outline: none; box-sizing: border-box; }
        .price-input:focus { border-color: var(--primary); }
        .day-badge { display: inline-block; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; margin-bottom: 1rem; }
        .badge-weekday { background: #e0f2fe; color: #0369a1; }
        .badge-weekend { background: #fef3c7; color: #92400e; }
        .btn-save { width: 100%; margin-top: 2rem; padding: 0.9rem; background: var(--dark); color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer; }
        .btn-save:hover { background: #457b9d; }
        .info-box { background: #f1f5f9; border-radius: 8px; padding: 1rem 1.2rem; margin-bottom: 1.5rem; color: var(--gray); font-size: 0.9rem; border-left: 4px solid var(--dark); }
    </style>
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="brand"><h2>GoKart Admin</h2><p>Management System</p></div>
            <ul class="nav-menu">
                <li><a href="{{ route('admin.dashboard') }}" class="nav-link"><span>Dashboard</span></a></li>
                <li><a href="{{ route('admin.riwayat_keuangan') }}" class="nav-link"><span>Riwayat Keuangan</span></a></li>
                <li><a href="{{ route('admin.input_waktu') }}" class="nav-link"><span>Input Waktu Balap</span></a></li>
                <li><a href="{{ route('admin.leaderboard.index') }}" class="nav-link "><span>Lihat Leaderboard</span></a></li>
                <li><a href="{{ route('admin.kelolapaket') }}" class="nav-link active"><span>Kelola Paket</span></a></li>
                <li><a href="{{ route('admin.kelolausers') }}" class="nav-link"><span>Kelola Users</span></a></li>
            </ul>
        </div>
        <div class="sidebar-bottom">
            <div class="sidebar-user"><p><strong>Admin Panel</strong></p><small>Race Director</small></div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <button class="logout-btn" onclick="if(confirm('Keluar dari panel admin?')) document.getElementById('logout-form').submit();"><span>Logout</span></button>
        </div>
    </aside>

    <main class="main">
        <header class="header">
            <h1>Manajemen Harga Bermain</h1>
            <p>Atur tarif sewa gokart per kart berdasarkan hari bermain</p>
        </header>

        @if(session('success'))
            <div class="alert alert-success" style="padding: 1rem; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600;">
                {{ session('success') }}
            </div>
        @endif

        <div class="info-box">
            Sistem harga otomatis menentukan tarif berdasarkan tanggal booking user. Harga berlaku <strong>per kart per sesi</strong>. Maksimal booking <strong>5 kart</strong> sekaligus.
        </div>

        <form action="{{ route('admin.paket.update_harga') }}" method="POST">
            @csrf
            <div class="price-grid">
                <div class="price-card">
                    <span class="day-badge badge-weekday">Senin – Jumat</span>
                    <h3>Harga Weekday</h3>
                    <small>Berlaku untuk hari kerja (non-libur nasional)</small>
                    <div class="price-input-wrap">
                        <span>Rp</span>
                        <input type="number" name="harga_weekday" class="price-input" value="{{ $harga_weekday }}" min="0" step="1000" required>
                    </div>
                </div>
                <div class="price-card weekend-card">
                    <span class="day-badge badge-weekend">Sabtu – Minggu</span>
                    <h3>Harga Weekend</h3>
                    <small>Berlaku untuk akhir pekan dan hari libur nasional</small>
                    <div class="price-input-wrap">
                        <span>Rp</span>
                        <input type="number" name="harga_weekend" class="price-input" value="{{ $harga_weekend }}" min="0" step="1000" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-save">Simpan Perubahan Harga</button>
        </form>

        <section class="card table-container" style="margin-top: 2rem;">
            <h3 style="border-left: 4px solid var(--dark); padding-left: 10px; margin-bottom: 1.5rem;">Preview Kalkulasi Harga</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Jenis Hari</th>
                        <th>Hari</th>
                        <th style="text-align:center;">1 Kart</th>
                        <th style="text-align:center;">2 Kart</th>
                        <th style="text-align:center;">3 Kart</th>
                        <th style="text-align:center;">4 Kart</th>
                        <th style="text-align:center;">5 Kart</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span style="background:#e0f2fe;color:#0369a1;padding:3px 8px;border-radius:4px;font-weight:700;font-size:0.85rem;">Weekday</span></td>
                        <td>Senin – Jumat</td>
                        @for ($i = 1; $i <= 5; $i++)
                            <td style="text-align:center;font-weight:600;">Rp {{ number_format($harga_weekday * $i, 0, ',', '.') }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td><span style="background:#fef3c7;color:#92400e;padding:3px 8px;border-radius:4px;font-weight:700;font-size:0.85rem;">Weekend</span></td>
                        <td>Sabtu – Minggu</td>
                        @for ($i = 1; $i <= 5; $i++)
                            <td style="text-align:center;font-weight:600;">Rp {{ number_format($harga_weekend * $i, 0, ',', '.') }}</td>
                        @endfor
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</div>
</body>
</html>