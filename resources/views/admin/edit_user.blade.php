<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Edit Profil User</title>
    <link rel="stylesheet" href="{{ asset('style-admin.css') }}">
    <style>
        .edit-container { max-width: 600px; margin: 2rem auto 0 auto; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 600; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 6px; outline: none; box-sizing: border-box; font-size: 0.95rem; }
        .form-control:focus { border-color: #1d3557; }
        .action-flex { display: flex; gap: 10px; margin-top: 1.5rem; }
        .btn-update { flex: 2; padding: 0.8rem; background-color: #1d3557; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 1rem; }
        .btn-update:hover { background-color: #457b9d; }
        .btn-kembali { flex: 1; padding: 0.8rem; background-color: #64748b; color: white; text-align: center; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 1rem; }
        .error-message { color: #dc2626; font-size: 0.85rem; margin-top: 0.2rem; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-top">
                <div class="brand"><h2>GoKart Admin</h2></div>
                <ul class="nav-menu">
                    <li><a href="{{ route('admin.kelolausers') }}" class="nav-link active"><span>Kembali ke Kelola User</span></a></li>
                </ul>
            </div>
        </aside>

        <main class="main">
            <header class="header">
                <h1>Edit Profil Pembalap</h1>
                <p>Mengubah data kredensial akun User ID #{{ $user->id }} secara manual</p>
            </header>

            <div class="edit-container">
                <div class="card">
                    <form action="{{ route('admin.kelolausers.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                            @error('nama_lengkap') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Nomor Handphone</label>
                            <input type="text" name="nomor_hp" class="form-control" value="{{ old('nomor_hp', $user->nomor_hp) }}" required>
                            @error('nomor_hp') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            @error('email') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Hak Akses / Role Level</label>
                            <select name="role" class="form-control">
                                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User (Pembalap)</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Race Director)</option>
                            </select>
                            @error('role') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Total Bermain (Counter Manual)</label>
                            <input type="number" name="total_bermain" class="form-control" value="{{ old('total_bermain', $user->total_bermain) }}" min="0" required>
                            @error('total_bermain') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="action-flex">
                            <a href="{{ route('admin.kelolausers') }}" class="btn-kembali">Batal</a>
                            <button type="submit" class="btn-update">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>