<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect pengguna ke halaman otorisasi OAuth Google.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback dari Google OAuth.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            $targetRoute = Auth::check() ? 'account.index' : 'login';

            return redirect()->route($targetRoute)->with('error', 'Gagal menghubungkan akun Google. Silakan coba kembali.');
        }

        $googleId = (string) $googleUser->getId();
        $googleEmail = $googleUser->getEmail();
        $googleName = $googleUser->getName() ?? $googleUser->getNickname() ?? 'Pengguna SIRA';
        $googleAvatar = $googleUser->getAvatar();

        // KASUS 1: Pengguna sudah login (Fitur Bind Akun Google)
        if (Auth::check()) {
            /** @var User $currentUser */
            $currentUser = Auth::user();

            // Periksa apakah Google ID sudah digunakan oleh pengguna lain
            $alreadyBound = User::where('google_id', $googleId)
                ->where('id', '!=', $currentUser->id)
                ->exists();

            if ($alreadyBound) {
                return redirect()->route('account.index')
                    ->with('error', 'Akun Google ini sudah ditautkan ke akun SIRA yang lain.');
            }

            // Periksa jika email dari Google sudah dipakai user lain
            if ($googleEmail) {
                $emailTaken = User::where('email', $googleEmail)
                    ->where('id', '!=', $currentUser->id)
                    ->exists();

                if ($emailTaken) {
                    return redirect()->route('account.index')
                        ->with('error', 'Email dari akun Google ini ('.$googleEmail.') sudah digunakan oleh akun lain.');
                }
            }

            $currentUser->google_id = $googleId;
            if (empty($currentUser->email) && $googleEmail) {
                $currentUser->email = $googleEmail;
            }
            if (empty($currentUser->google_avatar) && $googleAvatar) {
                $currentUser->google_avatar = $googleAvatar;
            }
            $currentUser->save();

            return redirect()->route('account.index')
                ->with('success', 'Akun Google berhasil dihubungkan ke profil Anda!');
        }

        // KASUS 2: Pengguna Belum Login
        // Cek 2A: Pengguna sudah pernah bind dengan google_id ini
        $user = User::where('google_id', $googleId)->first();
        if ($user) {
            if (empty($user->google_avatar) && $googleAvatar) {
                $user->google_avatar = $googleAvatar;
                $user->save();
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('reports.index'))
                ->with('success', 'Selamat datang kembali, '.$user->name.'!');
        }

        // Cek 2B: Pengguna belum memiliki google_id, tapi email Google cocok dengan akun yang ada
        if ($googleEmail) {
            $userByEmail = User::where('email', $googleEmail)->first();
            if ($userByEmail) {
                $userByEmail->google_id = $googleId;
                if (empty($userByEmail->google_avatar) && $googleAvatar) {
                    $userByEmail->google_avatar = $googleAvatar;
                }
                $userByEmail->save();

                Auth::login($userByEmail, true);
                $request->session()->regenerate();

                return redirect()->intended(route('reports.index'))
                    ->with('success', 'Akun Anda berhasil ditautkan dengan Google. Selamat datang kembali, '.$userByEmail->name.'!');
            }
        }

        // Cek 2C: Registrasi Baru via Google
        // Siapkan saran username awal dari email atau nama
        $suggestedUsername = '';
        if ($googleEmail) {
            $prefix = explode('@', $googleEmail)[0];
            $suggestedUsername = Str::slug($prefix, '_');
        }
        if (empty($suggestedUsername)) {
            $suggestedUsername = Str::slug($googleName, '_');
        }
        if (strlen($suggestedUsername) < 3) {
            $suggestedUsername = 'user_'.Str::lower(Str::random(4));
        }
        $suggestedUsername = substr($suggestedUsername, 0, 20);

        if (User::where('username', $suggestedUsername)->exists()) {
            $suggestedUsername = substr($suggestedUsername, 0, 15).'_'.rand(100, 999);
        }

        session([
            'google_registration' => [
                'google_id' => $googleId,
                'email' => $googleEmail,
                'name' => $googleName,
                'avatar' => $googleAvatar,
                'suggested_username' => $suggestedUsername,
            ],
        ]);

        return redirect()->route('auth.google.register');
    }

    /**
     * Tampilkan formulir penyelesaian registrasi akun Google.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        $googleData = session('google_registration');
        if (! $googleData) {
            return redirect()->route('login')
                ->with('error', 'Sesi pendaftaran Google tidak ditemukan atau telah kedaluwarsa.');
        }

        return view('auth.google-register', [
            'googleData' => $googleData,
        ]);
    }

    /**
     * Proses pendaftaran akun via Google (username dan nama wajib, password opsional).
     */
    public function completeRegistration(Request $request): RedirectResponse
    {
        $googleData = session('google_registration');
        if (! $googleData) {
            return redirect()->route('login')
                ->with('error', 'Sesi pendaftaran Google telah kedaluwarsa. Silakan coba lagi.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:30', 'unique:users,username'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'username.unique' => 'Username ini sudah digunakan, silakan pilih username lain.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda minus (-), dan garis bawah (_).',
            'password.min' => 'Kata sandi minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => $googleData['email'] ?? null,
            'google_id' => $googleData['google_id'],
            'google_avatar' => $googleData['avatar'] ?? null,
            'password' => filled($validated['password']) ? Hash::make($validated['password']) : null,
        ]);

        session()->forget('google_registration');

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('reports.index')
            ->with('success', 'Akun berhasil didaftarkan via Google! Selamat datang di SIRA, '.$user->name.'.');
    }

    /**
     * Lepas tautan akun Google (Unbind).
     */
    public function unbindGoogle(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->isGoogleBound()) {
            return redirect()->route('account.index')
                ->with('error', 'Akun Anda saat ini belum terhubung dengan Google.');
        }

        if (! $user->hasPassword()) {
            return redirect()->route('account.index')
                ->with('error', 'Anda harus mengatur kata sandi terlebih dahulu sebelum melepas akun Google, agar Anda tetap dapat login dengan username dan password.');
        }

        $user->google_id = null;
        $user->google_avatar = null;
        $user->save();

        return redirect()->route('account.index')
            ->with('success', 'Tautan akun Google berhasil dilepas.');
    }
}
