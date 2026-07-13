<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Racing - Profil Saya</title>
    <link rel="stylesheet" href="{{ asset('public/style-profil.css') }}"> 
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
                <p>Kelola informasi akun dan amankan kata sandi Anda</p>
            </header>

            <div class="profile-grid">
                <section class="card">
                    <h3 style="border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 1.5rem; color: var(--dark);">Data Diri Pembalap</h3>
                    
                    @if(session('success_profil'))
                        <div class="alert alert-success" style="color: green; margin-bottom: 1rem;">{{ session('success_profil') }}</div>
                    @endif

                    <form action="{{ route('profil.update') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Alamat Email (ID Akun)</label>
                            <input type="email" class="input-field input-disabled" value="{{ $user->email }}" readonly>
                            <small style="color: var(--gray); font-size: 0.75rem; display: block; margin-top: 0.2rem;">Email utama terkunci demi keamanan data transaksi.</small>
                        </div>

                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="input-field @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama_lengkap) }}" required>
                            @error('nama')
                                <small style="color: red;">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Nomor Telepon / WhatsApp</label>
                            <input type="text" name="no_telpon" class="input-field @error('no_telpon') is-invalid @enderror" value="{{ old('no_telpon', $user->nomor_hp) }}" placeholder="Contoh: 08123456789" required>
                            @error('no_telpon')
                                <small style="color: red;">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="save-btn" style="margin-top: 1rem;">Simpan Perubahan Profil</button>
                    </form>
                </section>

                <section class="card">
                    <h3 style="border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 1.5rem; color: var(--dark);">Keamanan & Kata Sandi</h3>
                    
                    @if(session('success_password'))
                        <div class="alert alert-success" style="color: green; margin-bottom: 1rem;">{{ session('success_password') }}</div>
                    @endif

                    <form action="{{ route('profil.password') }}" method="POST" id="passwordForm">
                        @csrf

                        <div class="form-group">
                            <label>Password Saat Ini</label>
                            <input type="password" name="password_lama" class="input-field @error('password_lama') is-invalid @enderror" placeholder="Masukkan password lama Anda" required>
                            @error('password_lama')
                                <small style="color: red;">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password_baru" id="passwordBaru" class="input-field @error('password_baru') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                            @error('password_baru')
                                <small style="color: red;">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi_password" id="konfirmasiPassword" class="input-field @error('konfirmasi_password') is-invalid @enderror" placeholder="Ulangi password baru Anda" required>
                            @error('konfirmasi_password')
                                <small style="color: red;">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="save-btn" style="margin-top: 1rem; background-color: var(--dark);">Perbarui Kata Sandi</button>
                    </form>
                </section>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
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