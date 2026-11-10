<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Tampilkan form permintaan tautan reset kata sandi.
     */
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim tautan reset kata sandi ke email pengguna.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email ini tidak terdaftar pada akun SIRA mana pun.',
            ]);
        }

        if (! $user->isGoogleBound()) {
            throw ValidationException::withMessages([
                'email' => 'Akun ini belum menautkan Google Account. Tautkan Google Account terlebih dahulu melalui Menu Akun agar dapat mereset kata sandi.',
            ]);
        }

        $status = Password::sendResetLink($validated);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Tautan reset kata sandi telah dikirim ke email Anda. Periksa kotak masuk atau folder spam.');
        }

        throw ValidationException::withMessages([
            'email' => match ($status) {
                Password::RESET_THROTTLED => 'Mohon tunggu sebentar sebelum meminta tautan reset lagi.',
                Password::INVALID_USER => 'Email ini tidak terdaftar pada akun SIRA mana pun.',
                default => 'Gagal mengirim tautan reset kata sandi. Silakan coba lagi.',
            },
        ]);
    }

    /**
     * Tampilkan form pengaturan kata sandi baru.
     */
    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    /**
     * Simpan kata sandi baru setelah token reset diverifikasi.
     */
    public function reset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password): void {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Kata sandi berhasil direset. Silakan masuk dengan kata sandi baru Anda.');
        }

        throw ValidationException::withMessages([
            'email' => match ($status) {
                Password::INVALID_TOKEN => 'Tautan reset kata sandi tidak valid atau telah kedaluwarsa.',
                Password::INVALID_USER => 'Email ini tidak terdaftar pada akun SIRA mana pun.',
                default => 'Gagal mereset kata sandi. Silakan coba lagi.',
            },
        ]);
    }
}
