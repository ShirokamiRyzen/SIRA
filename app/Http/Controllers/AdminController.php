<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Berikan atau cabut lencana verifikasi (verified badge) untuk pengguna atau akun lembaga daerah.
     */
    public function toggleVerify(Request $request, User $user): JsonResponse|RedirectResponse
    {
        if (! Auth::user()?->isAdmin()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya administrator yang dapat mengubah status verifikasi akun.',
                ], 403);
            }

            abort(403, 'Hanya administrator yang dapat mengubah status verifikasi akun.');
        }

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
}
