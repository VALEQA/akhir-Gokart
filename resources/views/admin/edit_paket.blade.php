<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Edit Paket</title>
    <link rel="stylesheet" href="{{ asset('style-admin.css') }}">
    <style>
        .edit-container { max-width: 600px; margin: 2rem auto 0 auto; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 600; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 6px; outline: none; box-sizing: border-box; font-size: 0.95rem; }
        .form-control:focus { border-color: #1d3557; }
        .action-flex { display: flex; gap: 10px; margin-top: 1.5rem; }
        .btn-update { flex: 2; padding: 0.8rem; background-color: #f59e0b; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 1rem; }
        .btn-update:hover { background-color: #d97706; }
        .btn-kembali { flex: 1; padding: 0.8rem; background-color: #64748b; color: white; text-align: center; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 1rem; }
        .btn-kembali:hover { background-color: #475569; }
        .error-message { color: #dc2626; font-size: 0.85rem; margin-top: 0.2rem; display: block; }
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
                    <li><a href="{{ route('admin.dashboard') }}" class="nav-link"><span>Riwayat Keuangan</span></a></li>
                    <li><a href="{{ route('admin.input_waktu') }}" class="nav-link"><span>Input Waktu Balap</span></a></li>
                    <li><a href="{{ route('admin.leaderboard.index') }}" class="nav-link "><span>Lihat Leaderboard</span></a></li>
                    <li><a href="{{ route('admin.kelolapaket') }}" class="nav-link active"><span>Kelola Paket</span></a></li>
                    <li><a href="{{ route('admin.dashboard') }}" class="nav-link"><span>Kelola Users</span></a></li>
                </ul>
            </div>
            <div class="sidebar-bottom">
                <div class="sidebar-user">
                    <p><strong>Admin Panel</strong></p>
                    <small>Race Director</small>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn" style="width: 100%; border: none; background: none; cursor: pointer; text-align: left;">
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Ubah Data Paket</h1>
                <p>Modifikasi detail informasi tarif dan kapasitas durasi paket #{{ $paket->id }}</p>
            </header>

            <div class="edit-container">
                @if(session('success'))
                    <div class="alert alert-success" style="background-color: #d1e7dd; color: #0f5132; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card">
                    <h3 style="margin-bottom: 1.5rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">✏️ Form Edit Paket</h3>
                    
                    <form action="{{ route('admin.paket.update', $paket->id) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Nama Paket</label>
                            <input type="text" name="nama_paket" class="form-control" value="{{ old('nama_paket', $paket->nama_paket) }}" required>
                            @error('nama_paket') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Deskripsi Paket</label>
                            <input type="text" name="deskripsi" class="form-control" value="{{ old('deskripsi', $paket->deskripsi) }}" required>
                            @error('deskripsi') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Durasi Bermain (Menit)</label>
                            <input type="number" name="durasi_menit" class="form-control" value="{{ old('durasi_menit', $paket->durasi_menit) }}" min="1" required>
                            @error('durasi_menit') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Maksimal Slot Orang</label>
                            <input type="number" name="maksimal_orang" class="form-control" value="{{ old('maksimal_orang', $paket->maksimal_orang) }}" min="1" required>
                            @error('maksimal_orang') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Harga Sewa Paket (Rp)</label>
                            <input type="number" name="harga" class="form-control" value="{{ old('harga', $paket->harga) }}" min="0" required>
                            @error('harga') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="action-flex">
                            <a href="{{ route('admin.kelolapaket') }}" class="btn-kembali">Batal</a>
                            <button type="submit" class="btn-update">Perbarui Paket</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>