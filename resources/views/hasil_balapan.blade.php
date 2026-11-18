<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>GoKart Racing - Hasil Balapan</title>
    <link rel="stylesheet" href="{{ asset('style-hasilbalapan.css') }}"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-top">
                <div class="brand">
                    <h2>FULL THROTTLE</h2>
                    <p>Booking System</p>
                </div>

            <ul class="nav-menu">
                <li><a href="{{ route('dashboard') }}" class="nav-link ">Dashboard</a></li>
                <li><a href="{{ route('booking') }}" class="nav-link">Booking</a></li>
                <li><a href="{{ route('open-match.index') }}" class="nav-link ">Open Match</a></li>
                <li><a href="{{ route('booking.history') }}" class="nav-link ">Riwayat Booking</a></li>
                <li><a href="{{ route('hasil-balapan.index') }}" class="nav-link active"><span>Hasil Balapan</span></a></li>
                <li><a href="{{ route('leaderboard.index') }}" class="nav-link "><span>Leaderboard</span></a></li>
                <li><a href="{{ route('pembayaran.index') }}" class="nav-link">Pembayaran</a></li>
                <li><a href="{{ route('profil.index') }}" class="nav-link "><span>Profil Saya</span></a></li>
            </ul>
            </div>
                <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong>{{ $user_logged->nama_lengkap }}</strong></p>
                    <small>{{ $user_logged->email }}</small>
                </div>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                
                <button type="button" class="logout-btn" onclick="if(confirm('Apakah Anda yakin ingin keluar?')) document.getElementById('logout-form').submit();">
                    Logout
                </button>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Hasil Balapan Saya</h1>
                <p>Analisis catatan waktu dan track record performa balap Anda</p>
            </header>

            @if (!empty($chart_data))
            <section class="card chart-container" style="margin-bottom: 2rem;">
                <h3>Tren Waktu Lap Tercepat (5 Balapan Terakhir)</h3>
                <div style="height: 250px; position: relative; margin-top: 15px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </section>
            @endif

            <section class="card table-container">
                <h3>Rincian Waktu Per Sektor</h3>
                <table class="racing-table">
                    <thead>
                        <tr>
                            <th>Tanggal & Sesi</th>
                            <th>Paket</th>
                            <th style="text-align: center;">Sektor 1</th>
                            <th style="text-align: center;">Sektor 2</th>
                            <th style="text-align: center;">Sektor 3</th>
                            <th style="text-align: right;">Total Lap</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hasil_balapan as $row)
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($row->tanggal_booking)->format('d M Y') }}</strong><br>
                                <small style="color: gray;">⏰ Sesi: {{ \Carbon\Carbon::parse($row->jam_booking)->format('H:i') }}</small>
                            </td>
                            <td><span class="badge-paket">{{ $row->nama_paket }}</span></td>
                            <td style="text-align: center;" class="sector-time">{{ number_format($row->sektor_1, 3) }}s</td>
                            <td style="text-align: center;" class="sector-time">{{ number_format($row->sektor_2, 3) }}s</td>
                            <td style="text-align: center;" class="sector-time">{{ number_format($row->sektor_3, 3) }}s</td>
                            <td style="text-align: right;" class="total-time-highlight">
                                {{ number_format($row->total_lap, 3) }} <span>detik</span>
                            </td>
                        
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: gray; padding: 3rem; font-style: italic;">
                                Anda belum memiliki catatan hasil balapan resmi. Selesaikan booking Anda untuk mencetak rekor!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <script src="{{ asset('script-hasil-balapan.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('performanceChart');
            if(ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {

                        labels: @json($chart_labels),
                        datasets: [{
                            label: 'Waktu Total Lap (Detik)',
                            data: @json($chart_data),
                            borderColor: '#e63946',
                            backgroundColor: 'rgba(230, 57, 70, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#1d3557',
                            pointHoverBackgroundColor: '#e63946',
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true, position: 'top' }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: { display: true, text: 'Durasi (Detik)', font: { weight: 'bold' } }
                            },
                            x: {
                                title: { display: true, text: 'Tanggal Balapan', font: { weight: 'bold' } }
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>