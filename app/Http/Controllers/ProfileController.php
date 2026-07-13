<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil user
     */
    public function index()
    {
        $user = Auth::user(); 
        return view('profil', compact('user'));
    }

    /**
     * Memproses update data diri pembalap
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:100',
            'no_telpon' => 'required|string|max:20',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'no_telpon.required' => 'Nomor telepon wajib diisi.',
        ]);

        $user->nama_lengkap = $request->nama;
        $user->nomor_hp = $request->no_telpon;
        $user->save();

        return redirect()->route('profil.index')->with('success_profil', 'Data diri pembalap berhasil diperbarui!');
    }

    /**
     * Memproses update password / keamanan akun
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|string|min:6',
            'konfirmasi_password' => 'required|same:password_baru',
        ], [
            'password_lama.required' => 'Password saat ini wajib diisi.',
            'password_baru.min' => 'Password baru harus berukuran minimal 6 karakter.',
            'konfirmasi_password.same' => 'Konfirmasi password tidak cocok dengan password baru.',
        ]);

        if (!Hash::check($request->password_lama, $user->password)) {
            throw ValidationException::withMessages([
                'password_lama' => 'Password saat ini yang Anda masukkan salah.',
            ]);
        }

        $user->password = $request->password_baru; // Otomatis ter-hash oleh Model
        $user->save();

        return redirect()->route('profil.index')->with('success_password', 'Kata sandi berhasil diperbarui!');
    }
}