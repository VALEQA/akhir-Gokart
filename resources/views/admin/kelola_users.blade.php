<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoKart Admin - Kelola Users</title>
    <link rel="stylesheet" href="{{ asset('public/style-admin.css') }}">
    <style>
        .badge-role { padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        .role-admin { background-color: #fecdd3; color: #9f1239; }
        .role-user { background-color: #e0f2fe; color: #0369a1; }
        .btn-edit { background-color: #f59e0b; color: white; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: inline-block; }
        .btn-delete { background-color: #ef4444; color: white; padding: 4px 10px; border: none; border-radius: 4px; font-size: 0.85rem; font-weight: 600; margin-left: 5px; cursor: pointer; display: inline-block; }
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
                    <li><a href="{{ route('admin.leaderboard.index') }}" class="nav-link "><span>Lihat Leaderboard</span></a></li>
                    <li><a href="{{ route('admin.kelolapaket') }}" class="nav-link"><span>Kelola Paket</span></a></li>
                    <li><a href="{{ route('admin.kelolausers') }}" class="nav-link active"><span>Kelola Users</span></a></li>
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
                <h1>Data Manajemen User</h1>
                <p>Melihat dan memperbarui profil biodata pendaftar sirkuit</p>
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

            <section class="card table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Lengkap</th>
                            <th>Kontak Email / HP</th>
                            <th style="text-align: center;">Role Level</th>
                            <th style="text-align: center;">Total Main</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($semua_user) == 0)
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--gray); padding: 3rem; font-style: italic;">
                                Belum ada user terdaftar di sistem.
                            </td>
                        </tr>
                        @else
                            @foreach($semua_user as $row)
                            <tr>
                                <td>#{{ $row->id }}</td>
                                <td><strong>{{ $row->nama_lengkap }}</strong></td>
                                <td>
                                    ✉️ {{ $row->email }}<br>
                                    📞 {{ $row->nomor_hp }}
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge-role {{ $row->role === 'admin' ? 'role-admin' : 'role-user' }}">
                                        {{ strtoupper($row->role) }}
                                    </span>
                                </td>
                                <td style="text-align: center; font-weight: bold;">{{ $row->total_bermain }} Kali</td>
                                <td style="text-align: center;">
                                    
                                    <a href="{{ route('admin.kelolausers.edit', $row->id) }}" class="btn-edit">Edit Profil</a>
                                    
                                    <form action="{{ route('admin.kelolausers.delete', $row->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Hapus user {{ $row->nama_lengkap }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">Hapus</button>
                                    </form>
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