<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Racing - Open Match</title>
    <link rel="stylesheet" href="{{ asset('style1.css') }}">
    <link rel="stylesheet" href="{{ asset('style-openmatch.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="brand"><h2>GoKart Racing</h2><p>Booking System</p></div>
            <ul class="nav-menu">
                <li><a href="{{ route('dashboard') }}" class="nav-link ">Dashboard</a></li>
                <li><a href="{{ route('booking') }}" class="nav-link">Booking</a></li>
                <li><a href="{{ route('open-match.index') }}" class="nav-link active">Open Match</a></li>
                <li><a href="{{ route('booking.history') }}" class="nav-link ">Riwayat Booking</a></li>
                <li><a href="{{ route('hasil-balapan.index') }}" class="nav-link "><span>Hasil Balapan</span></a></li>
                <li><a href="{{ route('leaderboard.index') }}" class="nav-link "><span>Leaderboard</span></a></li>
                <li><a href="{{ route('pembayaran.index') }}" class="nav-link">Pembayaran</a></li>
                <li><a href="{{ route('profil.index') }}" class="nav-link "><span>Profil Saya</span></a></li>
            </ul>
        </div>
        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <p><strong>{{ Auth::user()->nama_lengkap }}</strong></p>
                <small>{{ Auth::user()->email }}</small>
            </div>
            <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display:none;">
                @csrf
            </form>
            <button class="logout-btn" onclick="event.preventDefault(); if(confirm('Keluar dari sistem GoKart Racing?')) document.getElementById('logout-form').submit();">Logout</button>
        </div>
    </aside>

    <main class="main">
        <header class="page-header">
            <h1>🏁 Open Match</h1>
            <p>Cari lawan tanding dan bergabung ke sesi balapan yang terbuka</p>
        </header>

        <section class="card filter-section">
            <form method="GET" action="{{ route('open-match.index') }}" class="filter-form">
                <label class="form-label" for="filterTanggal">Filter Tanggal:</label>
                <div style="display:flex;gap:10px;align-items:center;">
                    <input type="date" id="filterTanggal" name="tanggal" class="input-field" 
                           value="{{ $filterTanggal }}" 
                           min="{{ date('Y-m-d') }}" style="max-width:250px;">
                    <button type="submit" class="btn btn-filter">🔍 Filter</button>
                    @if($filterTanggal)
                        <a href="{{ route('open-match.index') }}" class="btn btn-secondary btn-filter">✕ Reset</a>
                    @endif
                </div>
            </form>
        </section>

        <section class="open-match-section">
            <h2>🏎️ Sesi Open Match Tersedia</h2>
            <div class="open-match-grid">
                @forelse($openMatches as $row)
                    @php 
                        $slot_terisi = $row->jumlah_peserta;
                        $slot_total = $row->maks_slot_open;
                        $slot_sisa = $slot_total - $slot_terisi;
                        $first_name_host = explode(' ', $row->nama_lengkap)[0];
                    @endphp
                    <div class="card open-match-card" id="match-card-{{ $row->id }}">
                        <div class="match-header">
                            <div class="host-info">
                                <div class="host-avatar">{{ strtoupper(substr($first_name_host, 0, 1)) }}</div>
                                <div>
                                    <span class="host-name">{{ $first_name_host }}</span>
                                    <span class="match-badge">HOST</span>
                                </div>
                            </div>
                            <div class="slot-indicator">
                                <span class="slot-filled">{{ $row->jumlah_orang + $slot_terisi }}</span>
                                <span class="slot-separator">/</span>
                                <span class="slot-total">{{ $row->jumlah_orang + $slot_total }}</span>
                                <span class="slot-label">pemain</span>
                            </div>
                        </div>
                        
                        <div class="match-details">
                            <div class="detail-row"><span>📅</span><span>{{ date('d M Y', strtotime($row->tanggal_booking)) }}</span></div>
                            <div class="detail-row"><span>⏰</span><span>{{ date('H:i', strtotime($row->jam_booking)) }} WIB</span></div>
                            <div class="detail-row"><span>🏎️</span><span>{{ $row->nama_paket }} ({{ $row->durasi_menit }} menit)</span></div>
                            <div class="detail-row"><span>👥</span><span>Slot tersisa: <strong>{{ $slot_sisa }}</strong></span></div>
                        </div>

                        @if(!empty($row->catatan_open_match))
                            <div class="match-note">
                                <span>💬</span><em>{{ $row->catatan_open_match }}</em>
                            </div>
                        @endif

                        <div class="match-action">
                            @if($row->sudah_join)
                                <button class="btn btn-joined" disabled>✅ Sudah Bergabung</button>
                            @elseif($slot_sisa > 0)
                                <button class="btn btn-join" onclick="gabungMatch({{ $row->id }}, this)">🏁 GABUNG MATCH</button>
                            @else
                                <button class="btn btn-full" disabled>🔴 SLOT PENUH</button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="card empty-match">
                        <div class="empty-icon">🏁</div>
                        <h3>Belum Ada Open Match</h3>
                        <p>Belum ada sesi balapan terbuka saat ini.</p>
                        <a href="/booking" class="btn" style="max-width:250px;margin:1rem auto 0;display:block;text-align:center;text-decoration:none;">Buat Booking Baru</a>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="open-match-section" style="margin-top:2rem;">
            <h2>🤝 Partisipasi Saya</h2>
            <div class="open-match-grid">
                @forelse($partisipasi as $row)
                    @php $first_name_host = explode(' ', $row->nama_lengkap)[0]; @endphp
                    <div class="card open-match-card partisipasi-card" id="partisipasi-{{ $row->partisipasi_id }}">
                        <div class="match-header">
                            <div class="host-info">
                                <div class="host-avatar">{{ strtoupper(substr($first_name_host, 0, 1)) }}</div>
                                <div>
                                    <span class="host-name">{{ $first_name_host }}</span>
                                    <span class="match-badge badge-host">HOST</span>
                                </div>
                            </div>
                            <span class="match-badge badge-joined">BERGABUNG</span>
                        </div>
                        <div class="match-details">
                            <div class="detail-row"><span>📅</span><span>{{ date('d M Y', strtotime($row->tanggal_booking)) }}</span></div>
                            <div class="detail-row"><span>⏰</span><span>{{ date('H:i', strtotime($row->jam_booking)) }} WIB</span></div>
                            <div class="detail-row"><span>🏎️</span><span>{{ $row->nama_paket }} ({{ $row->durasi_menit }} menit)</span></div>
                            <div class="detail-row"><span>👥</span><span>Total Pemain: <strong>{{ $row->jumlah_orang + $row->jumlah_peserta }}</strong></span></div>
                        </div>
                        <div class="match-action">
                            <button class="btn btn-cancel" onclick="batalMatch({{ $row->partisipasi_id }}, this)">❌ Batalkan</button>
                        </div>
                    </div>
                @empty
                    <div class="card empty-match">
                        <div class="empty-icon">🤝</div>
                        <h3>Belum Ada Partisipasi</h3>
                        <p>Anda belum bergabung ke sesi Open Match manapun.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function gabungMatch(bookingId, btn) {
    if (!confirm('Yakin ingin bergabung ke sesi ini?')) return;
    
    btn.disabled = true;
    btn.textContent = 'Memproses...';
    
    const formData = new FormData();
    formData.append('booking_id', bookingId);
    
    fetch('{{ route("open-match.join") }}', { 
        method: 'POST', 
        body: formData,
        headers: { 'X-CSRF-TOKEN': csrfToken }
    })
    .then(r => r.json())
    .then(data => {
        if (data.sukses) {
            alert('🎉 ' + data.pesan);
            
            // Cek apakah ada URL pembayaran dari controller
            if (data.redirect_url) {
                window.location.href = data.redirect_url; // Alihkan otomatis ke halaman pembayaran 🚀
            } else {
                location.reload(); // Fallback jika URL tidak ditemukan
            }
        } else {
            alert('❌ Gagal: ' + data.pesan);
            btn.disabled = false;
            btn.textContent = '🏁 GABUNG MATCH';
        }
    })
    .catch(() => {
        alert('Koneksi gagal. Coba lagi.');
        btn.disabled = false;
        btn.textContent = '🏁 GABUNG MATCH';
    });
}

function batalMatch(partisipasiId, btn) {
    if (!confirm('Yakin ingin membatalkan partisipasi di sesi ini?')) return;
    
    btn.disabled = true;
    btn.textContent = 'Memproses...';
    
    const formData = new FormData();
    formData.append('partisipasi_id', partisipasiId);
    
    fetch('{{ route("open-match.leave") }}', { 
        method: 'POST', 
        body: formData,
        headers: { 'X-CSRF-TOKEN': csrfToken }
    })
    .then(r => r.json())
    .then(data => {
        if (data.sukses) {
            alert('✅ ' + data.pesan);
            location.reload(); // Refresh halaman untuk memperbarui status slot tempat bermain
        } else {
            alert('❌ Gagal: ' + data.pesan);
            btn.disabled = false;
            btn.textContent = '❌ Batalkan';
        }
    })
    .catch(() => {
        alert('Koneksi gagal. Coba lagi.');
        btn.disabled = false;
        btn.textContent = '❌ Batalkan';
    });
}
</script>
</body>
</html>