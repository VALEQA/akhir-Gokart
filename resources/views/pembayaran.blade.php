<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>GoKart Racing - Pembayaran</title>
    <link rel="stylesheet" href="{{ asset('style1.css') }}">
    
    <style>
        /* Tetapkan CSS bawaan asli Anda di sini */
        .bill-table-wrapper { overflow-x: auto; margin-top: 0.5rem; }
        .bill-table { width: 100%; border-collapse: collapse; }
        .bill-table th { padding: 0.8rem 1rem; color: var(--gray); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; border-bottom: 2px solid #eee; text-align: left; }
        .bill-table td { padding: 1.1rem 1rem; border-bottom: 1px solid #f1f1f1; vertical-align: middle; font-size: 0.95rem; }
        .bill-table tbody tr:hover { background-color: var(--bg-body); }
        .badge-paket { background-color: var(--primary-light); color: var(--primary); padding: 0.25rem 0.7rem; border-radius: 6px; font-size: 0.8rem; font-weight: 700; display: inline-block; }
        .btn-pay-inline { display: inline-block; padding: 0.45rem 1.1rem; background-color: var(--primary); color: var(--white); border-radius: 7px; font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: var(--transition); }
        .btn-pay-inline:hover { background-color: #c12e3a; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(230, 57, 70, 0.25); }
        .payment-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start; }
        @media (max-width: 992px) { .payment-detail-grid { grid-template-columns: 1fr; } }
        .bill-row { display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0; font-size: 0.95rem; border-bottom: 1px solid #f1f1f1; }
        .bill-row .label-text { color: var(--gray); }
        .bill-row .value-text { font-weight: 600; color: var(--dark); }
        .bill-total-row { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #eee; }
        .bill-total-row .total-label { font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: var(--gray); }
        .bill-total-row .total-amount { font-size: 1.8rem; font-weight: 800; color: var(--primary); }
        .payment-method-panel { text-align: center; padding: 2.5rem 1.5rem; }
        .payment-method-panel .method-icon { font-size: 3rem; margin-bottom: 1rem; display: block; }
        .payment-method-panel h3 { font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--dark); }
        .payment-method-panel p { color: var(--gray); font-size: 0.9rem; max-width: 280px; margin: 0 auto 2rem auto; }
        .btn-gateway { display: inline-block; width: 100%; padding: 1rem; background-color: var(--primary); color: var(--white); border: none; border-radius: 10px; font-size: 1.05rem; font-weight: 700; cursor: pointer; transition: var(--transition); }
        .btn-gateway:hover { background-color: #c12e3a; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(230, 57, 70, 0.3); }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-left: 4px solid #dc3545; padding: 1rem 1.2rem; border-radius: 8px; font-size: 0.9rem; text-align: left; }
        .alert-error code { display: block; margin-top: 0.3rem; font-size: 0.8rem; background: rgba(0,0,0,0.05); padding: 0.3rem 0.5rem; word-break: break-all; }
        .empty-state-box { text-align: center; padding: 4rem 2rem; }
        .empty-state-box .empty-icon { font-size: 3.5rem; display: block; margin-bottom: 1rem; }
        .empty-state-box h3 { color: var(--dark); margin-bottom: 0.5rem; }
        .back-link { display: inline-flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-weight: 600; margin-bottom: 1.5rem; transition: var(--transition); }
        .back-link:hover { gap: 10px; }
        .card-section-title { font-size: 1.1rem; font-weight: 700; color: var(--dark); border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 1.2rem; }

        /* === Upload bukti transfer, versi lebih rapi === */
        .upload-box {
            text-align: left;
            margin-bottom: 1.2rem;
            background: #fafafa;
            border: 1.5px dashed #ddd;
            border-radius: 12px;
            padding: 1.2rem;
            transition: var(--transition);
        }
        .upload-box:hover { border-color: var(--primary); background: #fff8f8; }
        .upload-box label.upload-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--dark);
            font-size: 0.95rem;
        }
        .file-picker {
            position: relative;
            display: block;
        }
        .file-picker input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .file-picker-visual {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--white);
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0.7rem 0.9rem;
            font-size: 0.85rem;
            color: var(--gray);
        }
        .file-picker-visual .file-picker-btn {
            flex-shrink: 0;
            background: var(--dark, #1d3557);
            color: #fff;
            font-weight: 600;
            font-size: 0.78rem;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            white-space: nowrap;
        }
        .file-picker-visual .file-picker-name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .upload-hint {
            font-size: 0.75rem;
            color: var(--gray);
            margin-top: 8px;
        }
        .divider-or {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1.4rem 0;
            color: var(--gray);
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
        .divider-or::before, .divider-or::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #eee;
        }
    </style>
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
                <li><a href="{{ route('hasil-balapan.index') }}" class="nav-link "><span>Hasil Balapan</span></a></li>
                <li><a href="{{ route('leaderboard.index') }}" class="nav-link "><span>Leaderboard</span></a></li>
                <li><a href="{{ route('pembayaran.index') }}" class="nav-link active">Pembayaran</a></li>
                <li><a href="{{ route('profil.index') }}" class="nav-link "><span>Profil Saya</span></a></li>
            </ul>
        </div>
        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <p><strong>{{ Auth::user()->nama_lengkap }}</strong></p>
                <small>{{ Auth::user()->email }}</small>
            </div>
            <form action="{{ route('logout') }}" method="POST" 
                onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem GoKart Racing?')">
                @csrf
                <button type="submit" class="logout-btn" style="width: 100%; border: none; cursor: pointer; text-align: left; background: none;">
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="main">
        <header class="header">
            <h1>Pusat Pembayaran</h1>
            <p>Selesaikan tagihan Anda dengan mudah</p>
        </header>

        @if($allBookings->count() > 0)

            @if(!$dataBooking)
                <section class="card">
                    <h3 class="card-section-title">Tagihan Menunggu Pembayaran</h3>
                    <div class="bill-table-wrapper">
                        <table class="bill-table">
                            <thead>
                                <tr>
                                    <th>ID Booking</th>
                                    <th>Paket</th>
                                    <th>Tanggal Sesi</th>
                                    <th>Pemain</th>
                                    <th style="text-align:right;">Total Tagihan</th>
                                    <th style="text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allBookings as $bill)
                                <tr>
                                    <td><strong>#BK-{{ str_pad($bill->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                                    <td><span class="badge-paket">{{ $bill->nama_paket }}</span></td>
                                    <td>
                                        {{ date('d M Y', strtotime($bill->tanggal_booking)) }}<br>
                                        <small style="color:var(--gray);">⏰ {{ date('H:i', strtotime($bill->jam_booking)) }} WIB</small>
                                    </td>
                                    <td>{{ $bill->jumlah_orang }} orang</td>
                                    <td style="text-align:right; font-weight:800; color:var(--primary); font-size:1.05rem;">
                                        Rp {{ number_format($bill->total_harga, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="{{ route('pembayaran.index', ['id' => $bill->id]) }}" class="btn-pay-inline">
                                            Bayar Sekarang
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @else
                <a href="{{ route('pembayaran.index') }}" class="back-link">← Kembali ke Daftar Tagihan</a>

                <div class="payment-detail-grid">
                    <section class="card">
                        <h3 class="card-section-title">Rincian Tagihan</h3>
                        <div class="bill-row">
                            <span class="label-text">ID Booking</span>
                            <span class="value-text">#BK-{{ str_pad($dataBooking->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="bill-row">
                            <span class="label-text">Nama Pemesan</span>
                            <span class="value-text">{{ $dataBooking->nama_lengkap }}</span>
                        </div>
                        <div class="bill-row">
                            <span class="label-text">Paket Balap</span>
                            <span class="value-text"><span class="badge-paket">{{ $dataBooking->nama_paket }}</span></span>
                        </div>
                        <div class="bill-row">
                            <span class="label-text">Tanggal Sesi</span>
                            <span class="value-text">{{ date('d F Y', strtotime($dataBooking->tanggal_booking)) }}</span>
                        </div>
                        <div class="bill-row">
                            <span class="label-text">Jam Sesi</span>
                            <span class="value-text">{{ date('H:i', strtotime($dataBooking->jam_booking)) }} WIB</span>
                        </div>
                        <div class="bill-row">
                            <span class="label-text">Jumlah Pemain</span>
                            <span class="value-text">{{ $dataBooking->jumlah_orang }} orang</span>
                        </div>
                        <div class="bill-total-row">
                            <span class="total-label">Total yang Harus Dibayar</span>
                            <span class="total-amount">Rp {{ number_format($dataBooking->total_harga, 0, ',', '.') }}</span>
                        </div>
                    </section>

                    <section class="card payment-method-panel">
                        <span class="method-icon">🏦</span>
                        <h3>Transfer Manual</h3>
                        <p>Silakan lakukan transfer sesuai total tagihan ke salah satu rekening di bawah ini:</p>

                        <div style="text-align: left; margin: 15px auto; max-width: 280px; background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #eee;">
                            <p style="margin: 0 0 10px 0; color: var(--dark);"><strong>Bank BCA:</strong><br>5211539616 (a.n Sakha Yusuf Pasha)</p>
                            <p style="margin: 0 0 10px 0; color: var(--dark);"><strong>Bank JAGO:</strong><br>506178941258 (a.n Sakha Yusuf Pasha)</p>
                            <p style="margin: 0; color: var(--dark);"><strong>DANA / GoPay / Shopeepay:</strong><br>082113996387</p>
                        </div>
                        
                        <p style="margin-top: 15px; margin-bottom: 10px; color: var(--dark);"><strong>Atau Scan QRIS:</strong></p>
                        <img src="{{ asset('qris.jpeg') }}" alt="QRIS Pembayaran" style="max-width: 200px; border-radius: 10px; border: 1px solid #ddd; padding: 5px; margin-bottom: 20px;">

                        @if(session('success'))
                            <div class="alert-error" style="background-color:#d4edda; color:#155724; border-color:#c3e6cb; border-left-color:#28a745; margin-bottom: 15px;">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert-error" style="margin-bottom: 15px;">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        @if(empty($dataBooking->bukti_transfer))
                            <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="booking_id" value="{{ $dataBooking->id }}">
                                <div class="upload-box">
                                    <label class="upload-title">📎 Upload Bukti Transfer</label>
                                    <div class="file-picker">
                                        <input type="file" name="bukti_transfer" id="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" required
                                            onchange="document.getElementById('fileNamePreview').textContent = this.files.length ? this.files[0].name : 'Belum ada file dipilih';">
                                        <div class="file-picker-visual">
                                            <span class="file-picker-btn">Pilih File</span>
                                            <span class="file-picker-name" id="fileNamePreview">Belum ada file dipilih</span>
                                        </div>
                                    </div>
                                    <p class="upload-hint">Format JPG, PNG, atau PDF · maks. 2MB</p>
                                </div>
                                <button type="submit" class="btn-gateway">
                                    📤 Kirim Bukti Pembayaran
                                </button>
                            </form>

                            <div class="divider-or">atau</div>
                        @else
                            <div class="alert-error" style="background-color:#fef3c7; color:#92400e; border-color:#fde68a; border-left-color:#d97706; margin-bottom: 15px;">
                                Bukti transfer sudah dikirim, menunggu verifikasi admin.
                            </div>
                        @endif

                        <a href="https://wa.me/6282113996387?text={{ urlencode('Halo Admin, saya sudah melakukan pembayaran untuk booking dengan nomor invoice #BK-' . str_pad($dataBooking->id, 3, '0', STR_PAD_LEFT) . ' sebesar Rp ' . number_format($dataBooking->total_harga, 0, ',', '.') . '. Mohon proses verifikasinya, terima kasih!') }}" 
                        target="_blank" 
                        class="btn-gateway" 
                        style="text-decoration: none; display: flex; align-items:center; justify-content:center; gap:8px; box-sizing: border-box; background-color:#fff; color:#25D366; border:1.5px solid #25D366;">
                        💬 Konfirmasi via WhatsApp
                        </a>
                    </section>
                </div>
            @endif

        @else
            <section class="card">
                <div class="empty-state-box">
                    <span class="empty-icon">🎉</span>
                    <h3>Tidak Ada Tagihan Aktif</h3>
                    <p>Semua booking Anda sudah lunas atau belum ada booking yang dibuat.</p>
                </div>
            </section>
        @endif
    </main>
</div>

</body>
</html>