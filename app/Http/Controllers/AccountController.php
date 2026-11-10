<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Tampilkan halaman pengaturan akun.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('account.index', [
            'user' => $user,
        ]);
    }

    /**
     * Perbarui informasi dasar akun pengguna (Nama & Email).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $user->name = $validated['name'];
        if (array_key_exists('email', $validated)) {
            $user->email = $validated['email'];
        }
        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Perbarui atau buat kata sandi baru.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasPassword()) {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ], [
                'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
                'password.min' => 'Kata sandi baru minimal 6 karakter.',
                'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            ]);
        } else {
            $validated = $request->validate([
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ], [
                'password.required' => 'Kata sandi baru wajib diisi.',
                'password.min' => 'Kata sandi minimal 6 karakter.',
                'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            ]);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        $message = $user->hasPassword()
            ? 'Kata sandi berhasil diperbarui.'
            : 'Kata sandi berhasil ditambahkan! Anda sekarang dapat masuk menggunakan username dan kata sandi.';

        return back()->with('success', $message);
    }
}
