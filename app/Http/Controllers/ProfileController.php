<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Daftar tingkatan rank pembalap.
     * "min" = jumlah minimal balapan (status booking = selesai) untuk mencapai rank tsb.
     * "image" = nama file badge jpg yang ada di public/img/ranks/
     * Urutan HARUS dari yang terkecil ke terbesar.
     */
    private function rankTiers(): array
    {
        return [
            ['key' => 'rookie',   'label' => 'Rookie Driver',   'min' => 0,  'image' => 'rookie.jpg',   'emoji' => '🔰'],
            ['key' => 'bronze',   'label' => 'Bronze Racer',    'min' => 5,  'image' => 'bronze.jpg',   'emoji' => '🥉'],
            ['key' => 'silver',   'label' => 'Silver Racer',    'min' => 15, 'image' => 'silver.jpg',   'emoji' => '🥈'],
            ['key' => 'gold',     'label' => 'Gold Racer',      'min' => 30, 'image' => 'gold.jpg',     'emoji' => '🥇'],
            ['key' => 'platinum', 'label' => 'Platinum Legend', 'min' => 50, 'image' => 'platinum.jpg', 'emoji' => '🏆'],
        ];
    }

    /**
     * Menentukan rank user saat ini + progress menuju rank berikutnya
     * berdasarkan jumlah total balapan (booking berstatus selesai).
     */
    private function resolveRank(int $totalMain): array
    {
        $tiers = $this->rankTiers();
        $tierCount = count($tiers);

        // Setiap tier diberi titik "checkpoint" yang jaraknya rata di sepanjang trek (0-100%)
        $stopGap = $tierCount > 1 ? 100 / ($tierCount - 1) : 100;
        foreach ($tiers as $i => &$tier) {
            $tier['stop'] = round($i * $stopGap, 2);
        }
        unset($tier);

        $currentIndex = 0;
        $current = $tiers[0];
        $next = null;

        foreach ($tiers as $i => $tier) {
            if ($totalMain >= $tier['min']) {
                $current = $tier;
                $currentIndex = $i;
                $next = $tiers[$i + 1] ?? null;
            }
        }

        $progress = 100;
        $sisaMenuju = 0;
        $trackPosition = $current['stop'];

        if ($next) {
            $range = $next['min'] - $current['min'];
            $progress = $range > 0 ? min(100, round((($totalMain - $current['min']) / $range) * 100)) : 100;
            $sisaMenuju = max(0, $next['min'] - $totalMain);
            $trackPosition = $current['stop'] + ($progress / 100) * ($next['stop'] - $current['stop']);
        }

        return [
            'current' => $current,
            'next' => $next,
            'progress' => $progress,
            'sisa_menuju' => $sisaMenuju,
            'total_main' => $totalMain,
            'all_tiers' => $tiers,
            'track_position' => round($trackPosition, 2),
        ];
    }

    /**
     * Menampilkan halaman profil user
     */
    public function index()
    {
        $user = Auth::user();

        $totalMain = DB::table('booking')
            ->where('user_id', $user->id)
            ->where('status', 'selesai')
            ->count();

        $rank = $this->resolveRank($totalMain);

        return view('profil', compact('user', 'rank'));
    }

    /**
     * Memproses update data diri pembalap (nama, no telp, dan foto profil)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:100',
            'no_telpon' => 'required|string|max:20',
            'foto_profil' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'no_telpon.required' => 'Nomor telepon wajib diisi.',
            'foto_profil.image' => 'File yang diunggah harus berupa gambar.',
            'foto_profil.mimes' => 'Format foto harus JPG, JPEG, atau PNG.',
            'foto_profil.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $user->nama_lengkap = $request->nama;
        $user->nomor_hp = $request->no_telpon;

        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama agar storage tidak menumpuk
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $path = $request->file('foto_profil')->store('foto_profil', 'public');
            $user->foto_profil = $path;
        }

        $user->save();

        return redirect()->route('profil.index')->with('success_profil', 'Data diri pembalap berhasil diperbarui!');
    }

    /**
     * Menghapus foto profil (kembali ke avatar default)
     */
    public function deletePhoto(Request $request)
    {
        $user = Auth::user();

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->foto_profil = null;
        $user->save();

        return redirect()->route('profil.index')->with('success_profil', 'Foto profil berhasil dihapus.');
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