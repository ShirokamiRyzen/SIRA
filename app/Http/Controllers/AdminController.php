<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Tampilkan halaman manajemen pengguna untuk administrator.
     */
    public function index(Request $request): View
    {
        abort_unless(Auth::user()?->isAdmin(), 403, 'Akses terbatas hanya untuk administrator.');

        $totalUsers = User::count();
        $totalVerified = User::where('is_verified', true)->count();
        $totalAdmins = User::where('is_admin', true)->orWhere('username', 'admin')->count();

        $query = User::withCount(['reports', 'comments'])->latest();

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $filter = (string) $request->query('filter', 'all');
        if ($filter === 'verified') {
            $query->where('is_verified', true);
        } elseif ($filter === 'unverified') {
            $query->where('is_verified', false)->where('is_admin', false)->where('username', '!=', 'admin');
        } elseif ($filter === 'admin') {
            $query->where(function ($q) {
                $q->where('is_admin', true)->orWhere('username', 'admin');
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users', 'totalUsers', 'totalVerified', 'totalAdmins', 'filter', 'search'));
    }

    /**
     * Berikan atau cabut lencana verifikasi (verified badge) untuk pengguna atau akun lembaga daerah.
     */
    public function toggleVerify(Request $request, User $user): JsonResponse|RedirectResponse
    {
        abort_unless(Auth::user()?->isAdmin(), 403, 'Hanya administrator yang dapat mengubah status verifikasi akun.');

        if ($user->isAdmin()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun administrator otomatis memiliki lencana verifikasi emas permanen.',
                ], 422);
            }

            return back()->with('error', 'Akun administrator otomatis memiliki lencana verifikasi emas permanen.');
        }

        $newVerifiedStatus = ! (bool) $user->is_verified;
        $user->update([
            'is_verified' => $newVerifiedStatus,
        ]);

        $statusText = $newVerifiedStatus ? 'diberikan lencana verifikasi' : 'dicabut lencana verifikasinya';
        $message = "Akun @{$user->username} berhasil {$statusText}.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'user_id' => $user->id,
                'is_verified' => $user->is_verified,
                'badge_type' => $user->badgeType(),
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Hapus akun pengguna dari sistem.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless(Auth::user()?->isAdmin(), 403, 'Hanya administrator yang dapat menghapus akun.');

        if ($user->id === Auth::id() || $user->username === 'admin') {
            return back()->with('error', 'Akun administrator utama tidak dapat dihapus.');
        }

        if (strtolower($user->username) === 'sira') {
            return back()->with('error', 'Akun bot asisten SIRA tidak dapat dihapus.');
        }

        $username = $user->username;
        $user->notifications()->delete();
        $user->delete();

        return back()->with('success', "Akun @{$username} berhasil dihapus dari sistem.");
    }
}
