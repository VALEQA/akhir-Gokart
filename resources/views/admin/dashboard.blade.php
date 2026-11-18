<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Dashboard</title>
    <link rel="stylesheet" href="{{ asset('style-admin.css') }}">
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
                        <li><a href="{{ route('admin.dashboard') }}" class="nav-link active"><span>Dashboard</span></a></li>
                        <li><a href="{{ route('admin.riwayat_keuangan') }}" class="nav-link"><span>Riwayat Keuangan</span></a></li>
                        <li><a href="{{ route('admin.input_waktu') }}" class="nav-link"><span>Input Waktu Balap</span></a></li>
                        <li><a href="{{ route('admin.leaderboard.index') }}" class="nav-link e"><span>Lihat Leaderboard</span></a></li>
                        <li><a href="{{ route('admin.kelolapaket') }}" class="nav-link"><span>Kelola Paket</span></a></li>
                        <li><a href="{{ route('admin.kelolausers') }}" class="nav-link "><span>Kelola Users</span></a></li>
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
                <h1>Overview Dashboard</h1>
                <p>Verifikasi pembayaran manual via konfirmasi WhatsApp secara real-time</p>
            </header>

            @if(session('success'))
                <div class="alert alert-success" style="padding: 1rem; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 1.5rem;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="padding: 1rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 1.5rem;">
                    {{ session('error') }}
                </div>
            @endif

            <section class="stats-grid">
                <div class="stat-card">
                    <span class="stat-icon">👥</span>
                    <div>
                        <h3>Total Racers</h3>
                        <p class="stat-number">{{ $total_users }} <span class="unit">orang</span></p>
                    </div>
                </div>
                <div class="stat-card alert-card">
                    <span class="stat-icon">🔔</span>
                    <div>
                        <h3>Butuh Validasi</h3>
                        <p class="stat-number">{{ $booking_masuk }} <span class="unit">transaksi</span></p>
                    </div>
                </div>
                <div class="stat-card success-card">
                    <span class="stat-icon">✅</span>
                    <div>
                        <h3>Sesi Selesai</h3>
                        <p class="stat-number">{{ $booking_selesai }} <span class="unit">sesi</span></p>
                    </div>
                </div>
                <div class="stat-card money-card">
                    <span class="stat-icon">💰</span>
                    <div>
                        <h3>Total Omset</h3>
                        <p class="stat-number">Rp {{ number_format($total_pendapatan ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </section>

            <section class="card table-container" style="margin-top: 2rem;">
                <h3 style="border-left: 4px solid var(--dark); padding-left: 10px; margin-bottom: 1.5rem;">Persetujuan Transaksi Masuk</h3>
                
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID Booking</th>
                                <th>Nama Pembalap</th>
                                <th>Paket Pilihan</th>
                                <th style="text-align: right;">Total Tagihan</th>
                                <th style="text-align: center;">Status Verifikasi</th>
                                <th style="text-align: center;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($pembayaran) == 0)
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                    Belum ada kiriman bukti transfer baru dari pembalap yang perlu divalidasi.
                                </td>
                            </tr>
                            @else
                                @foreach($pembayaran as $row)
                                <tr>
                                    <td><strong>#BK-{{ $row->id }}</strong></td>
                                    <td>
                                        <strong>{{ $row->nama_lengkap }}</strong><br>
                                        <small style="color: var(--gray);">HP: {{ $row->nomor_hp }}</small>
                                    </td>
                                    <td>
                                        <span class="badge-paket">{{ $row->nama_paket }}</span><br>
                                        <small>{{ date('d M Y', strtotime($row->tanggal_booking)) }} - Pukul {{ date('H:i', strtotime($row->jam_booking)) }}</small>
                                    </td>
                                    <td style="text-align: right; font-weight: bold; color: #1d3557;">
                                        Rp {{ number_format($row->total_harga, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ asset('' . $row->bukti_transfer) }}" target="_blank" style="color: #d97706; font-weight: bold; background: #fef3c7; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; text-decoration:none;">🔍 Lihat Bukti</a>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.dashboard.proses', ['action' => 'setujui', 'id' => $row->id]) }}" class="btn-approve" onclick="return confirm('Apakah uang transferan user ini benar-benar sudah masuk ke rekening?')">Terima</a>
                                            <a href="{{ route('admin.dashboard.proses', ['action' => 'tolak', 'id' => $row->id]) }}" class="btn-reject" onclick="return confirm('Tolak bukti transfer ini dan minta user kirim ulang?')">Tolak</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="card table-container" style="margin-top: 2rem;">
                <h3 style="border-left: 4px solid var(--dark); padding-left: 10px; margin-bottom: 1.5rem;">Konfirmasi Manual (Termasuk yang Hanya Chat WhatsApp)</h3>
                <p style="color: var(--gray); font-size: 0.85rem; margin-top: -0.8rem; margin-bottom: 1.2rem;">
                    Gunakan tabel ini kalau user bilang sudah transfer lewat WhatsApp tapi tidak upload bukti di web. Cari nama/invoice-nya lalu klik Terima langsung.
                </p>

                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID Booking</th>
                                <th>Nama Pembalap</th>
                                <th>Paket Pilihan</th>
                                <th style="text-align: right;">Total Tagihan</th>
                                <th style="text-align: center;">Bukti Upload?</th>
                                <th style="text-align: center;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($semuaMenunggu) == 0)
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                    Tidak ada booking yang masih menunggu pembayaran.
                                </td>
                            </tr>
                            @else
                                @foreach($semuaMenunggu as $row)
                                <tr>
                                    <td><strong>#BK-{{ $row->id }}</strong></td>
                                    <td>
                                        <strong>{{ $row->nama_lengkap }}</strong><br>
                                        <small style="color: var(--gray);">HP: {{ $row->nomor_hp }}</small>
                                    </td>
                                    <td>
                                        <span class="badge-paket">{{ $row->nama_paket }}</span><br>
                                        <small>{{ date('d M Y', strtotime($row->tanggal_booking)) }} - Pukul {{ date('H:i', strtotime($row->jam_booking)) }}</small>
                                    </td>
                                    <td style="text-align: right; font-weight: bold; color: #1d3557;">
                                        Rp {{ number_format($row->total_harga, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        @if(!empty($row->bukti_transfer))
                                            <a href="{{ asset('' . $row->bukti_transfer) }}" target="_blank" style="color: #d97706; font-weight: bold; background: #fef3c7; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; text-decoration:none;">🔍 Ada</a>
                                        @else
                                            <span style="color: #6b7280; font-weight: bold; background: #f3f4f6; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem;">💬 WA Saja</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.dashboard.proses', ['action' => 'setujui', 'id' => $row->id]) }}" class="btn-approve" onclick="return confirm('Apakah uang transferan user ini benar-benar sudah masuk ke rekening?')">Terima</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="card table-container" style="margin-top: 2rem;">
                <h3 style="border-left: 4px solid var(--dark); padding-left: 10px; margin-bottom: 1.5rem;">Persetujuan Pembayaran Partisipan Open Match</h3>
                <p style="color: var(--gray); font-size: 0.85rem; margin-top: -0.8rem; margin-bottom: 1.2rem;">
                    Ini tagihan orang-orang yang gabung ke Open Match punya orang lain (terpisah dari tagihan pemilik booking di atas).
                </p>

                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID Booking</th>
                                <th>Nama Peserta</th>
                                <th>Paket Pilihan</th>
                                <th style="text-align: right;">Total Tagihan</th>
                                <th style="text-align: center;">Status Verifikasi</th>
                                <th style="text-align: center;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($pembayaranPeserta) == 0)
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                    Belum ada pembayaran partisipan yang perlu divalidasi.
                                </td>
                            </tr>
                            @else
                                @foreach($pembayaranPeserta as $row)
                                <tr>
                                    <td><strong>#BK-{{ $row->booking_id }}</strong></td>
                                    <td>
                                        <strong>{{ $row->nama_lengkap }}</strong><br>
                                        <small style="color: var(--gray);">HP: {{ $row->nomor_hp }}</small>
                                    </td>
                                    <td>
                                        <span class="badge-paket">{{ $row->nama_paket }}</span><br>
                                        <small>{{ date('d M Y', strtotime($row->tanggal_booking)) }} - Pukul {{ date('H:i', strtotime($row->jam_booking)) }}</small>
                                    </td>
                                    <td style="text-align: right; font-weight: bold; color: #1d3557;">
                                        Rp {{ number_format($row->total_harga, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ asset('' . $row->bukti_transfer) }}" target="_blank" style="color: #d97706; font-weight: bold; background: #fef3c7; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; text-decoration:none;">🔍 Lihat Bukti</a>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.dashboard.proses_peserta', ['action' => 'setujui', 'id' => $row->id]) }}" class="btn-approve" onclick="return confirm('Apakah uang transferan peserta ini benar-benar sudah masuk ke rekening?')">Terima</a>
                                            <a href="{{ route('admin.dashboard.proses_peserta', ['action' => 'tolak', 'id' => $row->id]) }}" class="btn-reject" onclick="return confirm('Tolak bukti transfer peserta ini dan minta kirim ulang?')">Tolak</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="{{ asset('script.js') }}"></script>
</body>
</html>