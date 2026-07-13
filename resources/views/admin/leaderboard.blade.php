<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Leaderboard</title>
    <link rel="stylesheet" href="{{ asset('public/style-admin.css') }}">
    <style>
        /* Desain khusus panggung podium */
        .podium-badge {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-weight: bold;
            font-size: 0.9rem;
            text-align: center;
        }
        .pos-1 { background-color: #ffd700; color: #000; } /* Emas */
        .pos-2 { background-color: #c0c0c0; color: #000; } /* Perak */
        .pos-3 { background-color: #cd7f32; color: #fff; } /* Perunggu */
        .pos-regular { background-color: #f1f5f9; color: #334155; }
        
        .highlight-row { font-weight: 600; }
        .time-highlight { font-family: 'Courier New', Courier, monospace; font-size: 1.1rem; color: #e63946; font-weight: bold; }
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
                    <li><a href="{{ route('admin.input_waktu') }}" class="nav-link"><span>Input Waktu Balap</span></a></li>
                    <li><a href="{{ route('leaderboard.index') }}" class="nav-link active"><span>Lihat Leaderboard</span></a></li>
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
                <h1>Sirkuit Leaderboard</h1>
                <p>Peringkat urutan pembalap tercepat berdasarkan akumulasi waktu 3 sektor sirkuit</p>
            </header>

            <section class="card table-container">
                <h3 style="border-left: 4px solid #ffd700; padding-left: 10px; margin-bottom: 1.5rem;">👑 Top Speed Records</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 80px;">Posisi</th>
                            <th>Nama Pembalap</th>
                            <th>Paket Sesi</th>
                            <th style="text-align: center;">Sektor 1</th>
                            <th style="text-align: center;">Sektor 2</th>
                            <th style="text-align: center;">Sektor 3</th>
                            <th style="text-align: right; padding-right: 2rem;">Best Lap Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($leaderboard) == 0)
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                Belum ada catatan waktu balap yang memenuhi kualifikasi rekor sirkuit.
                            </td>
                        </tr>
                        @else
                            @foreach ($leaderboard as $row)
                                @php
                                    // Menentukan posisi peringkat dari iterasi loop Laravel
                                    $posisi = $loop->iteration;
                                    if ($posisi == 1) { $badge_class = "pos-1"; $badge_text = "🥇 1st"; }
                                    elseif ($posisi == 2) { $badge_class = "pos-2"; $badge_text = "🥈 2nd"; }
                                    elseif ($posisi == 3) { $badge_class = "pos-3"; $badge_text = "🥉 3rd"; }
                                    else { $badge_class = "pos-regular"; $badge_text = $posisi . "th"; }
                                @endphp
                                <tr class="{{ $posisi <= 3 ? 'highlight-row' : '' }}">
                                    <td style="text-align: center;">
                                        <span class="podium-badge {{ $badge_class }}">{{ $badge_text }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $row->nama_lengkap }}</strong><br>
                                        <small style="color: var(--gray);">ID Booking: #BK-{{ $row->booking_id }}</small>
                                    </td>
                                    <td>
                                        <span>{{ $row->nama_paket }}</span><br>
                                        <small>{{ date('d M Y', strtotime($row->tanggal_booking)) }}</small>
                                    </td>
                                    <td style="text-align: center; color: var(--gray);">{{ number_format($row->sektor_1, 3) }}s</td>
                                    <td style="text-align: center; color: var(--gray);">{{ number_format($row->sektor_2, 3) }}s</td>
                                    <td style="text-align: center; color: var(--gray);">{{ number_format($row->sektor_3, 3) }}s</td>
                                    <td style="text-align: right; padding-right: 2rem;" class="time-highlight">
                                        {{ number_format($row->total_lap, 3) }} s
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