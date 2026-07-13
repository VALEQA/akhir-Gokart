<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Memanggil model User
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * 1. Menampilkan semua akun user (Daftar / Tabel Users)
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Urutkan berdasarkan role (admin dulu) lalu nama_lengkap
        $semua_user = User::orderBy('role', 'asc')->orderBy('nama_lengkap', 'asc')->get();

        return view('admin.kelola_users', compact('semua_user'));
    }

    /**
     * 2. Menampilkan form edit profil user berdasarkan ID
     */
    public function edit($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Ambil data user, jika ID salah/tidak ada langsung otomatis 404
        $user = User::findOrFail($id);

        return view('admin.edit_user', compact('user'));
    }

    /**
     * 3. Memproses pembaruan data profil user (proses submit form)
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $user = User::findOrFail($id);

        // Validasi input
        $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'nomor_hp'      => 'required|string|max:20',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'role'          => 'required|in:user,admin',
            'total_bermain' => 'required|integer|min:0',
        ]);

        // Simpan ke database
        $user->update([
            'nama_lengkap'  => $request->nama_lengkap,
            'nomor_hp'      => $request->nomor_hp,
            'email'         => $request->email,
            'role'          => $request->role,
            'total_bermain' => $request->total_bermain,
        ]);

        return redirect()->route('admin.kelolausers')->with('success', 'Profil user berhasil diperbarui dari pusat!');
    }

    /**
     * 4. Menghapus akun user
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $user_login = Auth::user();
        $user_hapus = User::findOrFail($id);

        if ($user_hapus->id === $user_login->id || $user_hapus->id === 1) {
            return redirect()->back()->with('error', 'Gagal! Kamu tidak bisa menghapus akun Utama Admin ini.');
        }

        $user_hapus->delete();

        return redirect()->route('admin.kelolausers')->with('success', 'User berhasil didelete dari sistem.');
    }
}