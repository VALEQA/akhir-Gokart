<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Racing - Profil Saya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style-profil.css') }}">
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
                    <li><a href="{{ route('dashboard') }}" class="nav-link"><span>Dashboard</span></a></li>
                    <li><a href="{{ route('booking') }}" class="nav-link"><span>Booking</span></a></li>
                    <li><a href="{{ route('open-match.index') }}" class="nav-link">Open Match</a></li>
                    <li><a href="{{ route('booking.history') }}" class="nav-link"><span>Riwayat Booking</span></a></li>
                    <li><a href="{{ route('hasil-balapan.index') }}" class="nav-link"><span>Hasil Balapan</span></a></li>
                    <li><a href="{{ route('leaderboard.index') }}" class="nav-link"><span>Leaderboard</span></a></li>
                    <li><a href="{{ route('pembayaran.index') }}" class="nav-link"><span>Pembayaran</span></a></li>
                    <li><a href="{{ route('profil.index') }}" class="nav-link active"><span>Profil Saya</span></a></li>
                </ul>
            </div>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong>{{ $user->nama_lengkap }}</strong></p>
                    <small>{{ $user->email }}</small>
                </div>
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem GoKart Racing?')">
                    @csrf
                    <button type="submit" class="logout-btn" style="width: 100%; border: none; cursor: pointer; text-align: left; background: none;">
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Profil Saya</h1>
                <p>Kartu lisensi, progres rank, dan pengaturan akun pembalap Anda</p>
            </header>

            @if(session('success_profil'))
                <div class="alert alert-success">✔ {{ session('success_profil') }}</div>
            @endif
            @if(session('success_password'))
                <div class="alert alert-success">✔ {{ session('success_password') }}</div>
            @endif

            {{-- ===================== KARTU LISENSI PEMBALAP ===================== --}}
            <section class="license-card">
                <div class="license-flag-strip" aria-hidden="true"></div>

                <div class="license-body">
                    <div class="license-photo-block">
                        <div class="avatar-hex">
                            <img
                                src="{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : asset('img/default-avatar.png') }}"
                                alt="Foto Profil {{ $user->nama_lengkap }}"
                                id="avatarPreview"
                                onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 fill=%22%231d3557%22/><text x=%2250%22 y=%2262%22 font-size=%2244%22 text-anchor=%22middle%22 fill=%22%23ffffff%22 font-family=%22sans-serif%22>{{ strtoupper(substr($user->nama_lengkap,0,1)) }}</text></svg>'"
                            >
                        </div>

                        <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" id="photoForm" class="photo-form">
                            @csrf
                            <label for="fotoInput" class="avatar-edit-btn" title="Ganti foto profil">📷</label>
                            <input type="file" name="foto_profil" id="fotoInput" accept="image/png, image/jpeg, image/jpg" hidden>
                            <input type="hidden" name="nama" value="{{ $user->nama_lengkap }}">
                            <input type="hidden" name="no_telpon" value="{{ $user->nomor_hp }}">
                        </form>

                        @if($user->foto_profil)
                            <form action="{{ route('profil.photo.delete') }}" method="POST" onsubmit="return confirm('Hapus foto profil dan kembali ke avatar default?')">
                                @csrf
                                <button type="submit" class="avatar-remove-link">Hapus foto</button>
                            </form>
                        @endif

                        @error('foto_profil')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="license-info-block">
                        <span class="license-eyebrow">GoKart Racing &middot; Lisensi Pembalap Resmi</span>
                        <h2 class="license-name">{{ $user->nama_lengkap }}</h2>
                        <span class="license-id">NO. LISENSI &nbsp;GKR-{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</span>

                        <div class="license-meta-row">
                            <div class="license-meta-item">
                                <small>Email</small>
                                <span>{{ $user->email }}</span>
                            </div>
                            <div class="license-meta-item">
                                <small>Telepon</small>
                                <span>{{ $user->nomor_hp ?? '-' }}</span>
                            </div>
                            @if($user->created_at)
                            <div class="license-meta-item">
                                <small>Bergabung</small>
                                <span>{{ $user->created_at->translatedFormat('M Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="license-rank-block">
                        <div class="rank-medal" data-emoji="{{ $rank['current']['emoji'] }}">
                            <img src="{{ asset('img/ranks/' . $rank['current']['image']) }}" alt="{{ $rank['current']['label'] }}"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="rank-medal-fallback">{{ $rank['current']['emoji'] }}</span>
                        </div>
                        <span class="license-rank-label">{{ $rank['current']['label'] }}</span>
                        <span class="license-rank-count">{{ $rank['total_main'] }} balapan selesai</span>
                    </div>
                </div>
            </section>

            {{-- ===================== TREK PROGRES RANK ===================== --}}
            <section class="card rank-card">
                <div class="rank-card-head">
                    <h3 class="card-title">Progres Rank</h3>
                    @if($rank['next'])
                        <span class="rank-next-hint">{{ $rank['sisa_menuju'] }} balapan lagi menuju <strong>{{ $rank['next']['label'] }}</strong></span>
                    @else
                        <span class="rank-next-hint rank-maxed">🏆 Anda sudah di puncak klasemen!</span>
                    @endif
                </div>

                <div class="race-track" style="--track-position: {{ $rank['track_position'] }}%;">
                    <div class="race-track-road"></div>
                    <div class="race-track-fill" style="width: {{ $rank['track_position'] }}%;"></div>
                    <div class="race-track-kart" style="left: {{ $rank['track_position'] }}%;" title="Posisi Anda">🏎️</div>

                    @foreach($rank['all_tiers'] as $tier)
                        <div class="race-checkpoint {{ $tier['key'] === $rank['current']['key'] ? 'reached' : '' }} {{ $rank['total_main'] >= $tier['min'] ? 'reached' : '' }}"
                             style="left: {{ $tier['stop'] }}%;">
                            <div class="race-checkpoint-badge">
                                <img src="{{ asset('img/ranks/' . $tier['image']) }}" alt="{{ $tier['label'] }}"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <span class="rank-medal-fallback small">{{ $tier['emoji'] }}</span>
                            </div>
                            <span class="race-checkpoint-label">{{ $tier['label'] }}</span>
                            <small class="race-checkpoint-min">{{ $tier['min'] }}+</small>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- ===================== FORM DATA & KEAMANAN ===================== --}}
            <div class="profile-grid">
                <section class="card">
                    <h3 class="card-title">🏁 Data Diri Pembalap</h3>

                    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label>Alamat Email (ID Akun)</label>
                            <input type="email" class="input-field input-disabled" value="{{ $user->email }}" readonly>
                            <small class="field-hint">Email utama terkunci demi keamanan data transaksi.</small>
                        </div>

                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="input-field @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama_lengkap) }}" required>
                            @error('nama')
                                <small class="field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Nomor Telepon / WhatsApp</label>
                            <input type="text" name="no_telpon" class="input-field @error('no_telpon') is-invalid @enderror" value="{{ old('no_telpon', $user->nomor_hp) }}" placeholder="Contoh: 08123456789" required>
                            @error('no_telpon')
                                <small class="field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="save-btn">Simpan Perubahan Profil</button>
                    </form>
                </section>

                <section class="card">
                    <h3 class="card-title">🔒 Keamanan &amp; Kata Sandi</h3>

                    <form action="{{ route('profil.password') }}" method="POST" id="passwordForm">
                        @csrf

                        <div class="form-group">
                            <label>Password Saat Ini</label>
                            <input type="password" name="password_lama" class="input-field @error('password_lama') is-invalid @enderror" placeholder="Masukkan password lama Anda" required>
                            @error('password_lama')
                                <small class="field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password_baru" id="passwordBaru" class="input-field @error('password_baru') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                            @error('password_baru')
                                <small class="field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi_password" id="konfirmasiPassword" class="input-field @error('konfirmasi_password') is-invalid @enderror" placeholder="Ulangi password baru Anda" required>
                            @error('konfirmasi_password')
                                <small class="field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="save-btn save-btn-dark">Perbarui Kata Sandi</button>
                    </form>
                </section>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const fotoInput = document.getElementById("fotoInput");
            const photoForm = document.getElementById("photoForm");
            const avatarPreview = document.getElementById("avatarPreview");

            if (fotoInput && photoForm) {
                fotoInput.addEventListener("change", () => {
                    const file = fotoInput.files[0];
                    if (!file) return;

                    if (file.size > 2 * 1024 * 1024) {
                        alert("Ukuran foto maksimal 2MB.");
                        fotoInput.value = "";
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (avatarPreview) avatarPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    photoForm.submit();
                });
            }

            const passwordForm = document.getElementById("passwordForm");
            const passwordBaru = document.getElementById("passwordBaru");
            const konfirmasiPassword = document.getElementById("konfirmasiPassword");

            if (passwordForm) {
                passwordForm.addEventListener("submit", (e) => {
                    if (passwordBaru.value.length < 6) {
                        e.preventDefault();
                        alert("Gagal! Password baru harus berukuran minimal 6 karakter.");
                        return;
                    }

                    if (passwordBaru.value !== konfirmasiPassword.value) {
                        e.preventDefault();
                        alert("Gagal! Input Password Baru dan Konfirmasi Password tidak cocok.");
                    }
                });
            }
        });
    </script>
</body>
</html>
