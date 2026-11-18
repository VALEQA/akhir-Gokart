@php
    /** * @var array $sector_titles 
     * @var string $kategori 
     */
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Racing - Leaderboard</title>
    <link rel="stylesheet" href="{{ asset('style-leaderboard.css') }}">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-top">
                <div class="brand">
                    <h2>GoKart Racing</h2>
                    <p>Booking System</p>
                </div>

            <ul class="nav-menu">
                <li><a href="{{ route('dashboard') }}" class="nav-link ">Dashboard</a></li>
                <li><a href="{{ route('booking') }}" class="nav-link">Booking</a></li>
                <li><a href="{{ route('open-match.index') }}" class="nav-link ">Open Match</a></li>
                <li><a href="{{ route('booking.history') }}" class="nav-link ">Riwayat Booking</a></li>
                <li><a href="{{ route('hasil-balapan.index') }}" class="nav-link "><span>Hasil Balapan</span></a></li>
                <li><a href="{{ route('leaderboard.index') }}" class="nav-link active"><span>Leaderboard</span></a></li>
                <li><a href="{{ route('pembayaran.index') }}" class="nav-link">Pembayaran</a></li>
                <li><a href="{{ route('profil.index') }}" class="nav-link "><span>Profil Saya</span></a></li>   
            </ul>
            </div>

            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong>{{ $user_logged->nama_lengkap }}</strong></p>
                    <small>{{ $user_logged->email }}</small>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Top Racers Leaderboard</h1>
                <p>Daftar catatan waktu terbaik pembalap GoKart</p>
            </header>

            <div class="leaderboard-tabs">
                @foreach ($sector_titles as $key => $title)
                    <a href="{{ route('leaderboard.index', ['view' => $key]) }}" class="tab-btn {{ $kategori === $key ? 'active' : '' }}">
                        {{ $title }}
                    </a>
                @endforeach
            </div>

            <section class="card table-container">
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th style="width: 100px; text-align: center;">Posisi</th>
                            <th>Nama Pembalap</th>
                            <th>Email</th>
                            <th style="text-align: right;">Waktu Terbaik ({{ $sector_titles[$kategori] }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse ($leaderboards as $row)
                            <tr class="{{ $no <= 3 ? 'podium-row' : '' }}">
                                <td style="text-align: center;">
                                    <span class="rank-badge rank-{{ $no }}">{{ $no }}</span>
                                </td>
                                <td><strong>{{ $row->nama_lengkap }}</strong></td>
                                <td>{{ $row->email }}</td>
                                <td class="time-highlight" style="text-align: right;">
                                    {{ number_format($row->waktu_terbaik, 3) }} <span style="font-size: 0.8rem; font-weight: normal; color: gray;">detik</span>
                                </td>
                            </tr>
                            @php $no++; @endphp
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: gray; padding: 3rem; font-style: italic;">
                                    Belum ada catatan waktu resmi untuk kategori {{ $sector_titles[$kategori] }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>