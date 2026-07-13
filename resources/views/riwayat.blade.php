<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - GoKart Racing</title>
    <link rel="stylesheet" href="{{ asset('public/style1.css') }}">
    <link rel="stylesheet" href="{{ asset('public/riwayat_booking.css') }}">
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <div class="brand">
            <h2>GoKart Racing</h2>
            <p>Booking System</p>
        </div>
            <ul class="nav-menu">
                <li><a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a></li>
                <li><a href="{{ route('booking') }}" class="nav-link ">Booking</a></li>
                <li><a href="{{ route('open-match.index') }}" class="nav-link ">Open Match</a></li>
                <li><a href="{{ route('booking.history') }}" class="nav-link active">Riwayat Booking</a></li>
                <li><a href="{{ route('hasil-balapan.index') }}" class="nav-link "><span>Hasil Balapan</span></a></li>
                <li><a href="{{ route('leaderboard.index') }}" class="nav-link "><span>Leaderboard</span></a></li>
                <li><a href="{{ route('pembayaran.index') }}" class="nav-link">Pembayaran</a></li>
                <li><a href="{{ route('profil.index') }}" class="nav-link "><span>Profil Saya</span></a></li>
            </ul>
        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <p><strong>{{ $user_logged->nama_lengkap ?? $user_logged->name }}</strong></p>
                <small>{{ $user_logged->email }}</small>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn" onclick="return confirm('Yakin ingin keluar?')">Logout</button>
            </form>
        </div>
    </aside>

    <main class="main">
        <header class="page-header">
            <h1>Riwayat Booking Anda</h1>
            <p>Pantau status jadwal balapan dan tiket gokart Anda di sini</p>
        </header>

        @if(session('msg'))
            @php
                $notif = ''; $type = '';
                switch(session('msg')) {
                    case 'cancel_success': $notif = '✅ Booking berhasil dibatalkan.'; $type = 'success'; break;
                    case 'cancel_failed': $notif = '❌ Gagal membatalkan booking.'; $type = 'danger'; break;
                    case 'cannot_cancel': $notif = '⚠️ Booking tidak dapat dibatalkan karena sudah diproses/dibayar.'; $type = 'warning'; break;
                    case 'notfound': $notif = '⚠️ Data booking tidak ditemukan.'; $type = 'warning'; break;
                }
            @endphp
            @if($notif)
                <div class="notif-bar {{ $type }}" style="padding: 12px; margin-bottom: 20px; border-radius: 8px; font-weight: 600; background-color: {{ $type == 'success' ? '#d1e7dd' : ($type == 'danger' ? '#f8d7da' : '#fff3cd') }}; color: {{ $type == 'success' ? '#0f5132' : ($type == 'danger' ? '#842029' : '#664d03') }};">
                    {{ $notif }}
                </div>
            @endif
        @endif

        <div class="table-responsive">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th>No Invoice</th>
                        <th>Paket</th>
                        <th>Tanggal Main</th>
                        <th>Jam Sesi</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
    @if($riwayat->isNotEmpty())
        @foreach($riwayat as $row)
            @php
                $status = strtolower($row->status);
                $can_cancel = ($status === 'aktif' && empty($row->bukti_transfer));
            @endphp
            <tr>
                <td><strong>#BK{{ str_pad($row->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                <td>{{ htmlspecialchars($row->nama_paket) }}</td>
                <td>{{ date('d M Y', strtotime($row->tanggal_booking)) }}</td>
                <td>{{ date('H:i', strtotime($row->jam_booking)) }} WIB</td>
                <td><strong>Rp {{ number_format($row->total_harga, 0, ',', '.') }}</strong></td>
                <td>
                    <span class="status-badge {{ $status }}">
                        {{ ucfirst($row->status) }}
                    </span>
                </td>
                <td>
                    <div class="btn-action-group">
                        <a href="{{ route('booking.detail', $row->id) }}" class="btn-detail">Detail</a>
                        @if($can_cancel)
                            <button class="btn-cancel" onclick="confirmCancel({{ $row->id }}, '{{ date('d M Y', strtotime($row->tanggal_booking)) }}', '{{ htmlspecialchars($row->nama_paket) }}')">
                                Batalkan
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="7">
                <div class="empty-state">
                    <span class="empty-icon">🏁</span>
                    <p>Anda belum pernah melakukan booking gokart.</p>
                </div>
            </td>
        </tr>
    @endif
</tbody>
            </table>
        </div>
    </main>
</div>

<div class="modal-overlay" id="cancelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 9999;">
    <div class="modal-box" style="background: white; padding: 25px; border-radius: 12px; max-width: 450px; width: 90%; text-align: center;">
        <span style="font-size: 2.5rem; display: block; margin-bottom: 10px;">🚫</span>
        <h3 style="margin-bottom: 10px;">Batalkan Booking?</h3>
        <p id="modalDesc" style="font-size: 0.9rem; color: #6c757d; margin-bottom: 20px;">Apakah Anda yakin?</p>
        <div class="modal-actions" style="display: flex; gap: 10px; justify-content: center;">
            <button class="modal-btn-cancel" style="padding: 10px 20px; background: #f1faee; border: 1px solid #dee2e6; border-radius: 6px; cursor: pointer;" onclick="closeModal()">Tidak, Kembali</button>
            <form id="cancelForm" method="POST" action="">
                @csrf
                <button type="submit" class="modal-btn-confirm" style="padding: 10px 20px; background: #e63946; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Ya, Batalkan</button>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmCancel(bookingId, tanggal, paket) {
        document.getElementById('modalDesc').textContent = `Yakin membatalkan booking #BK${String(bookingId).padStart(3,'0')} — paket "${paket}" pada ${tanggal}? Tindakan ini tidak dapat diurungkan.`;
        // Set action form ke route pembatalan laravel
        document.getElementById('cancelForm').action = `/booking/batalkan/${bookingId}`;
        document.getElementById('cancelModal').style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('cancelModal').style.display = 'none';
    }
</script>
</body>
</html>