<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationController extends Controller
{
    /**
     * Dapatkan daftar notifikasi terkini dan jumlah yang belum dibaca (JSON).
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $notifications = $user->notifications()->take(10)->get();
        $unreadCount = $user->unreadNotifications()->count();

        $formatted = $notifications->map(function ($n) {
            return [
                'id' => $n->id,
                'read' => $n->read_at !== null,
                'created_at_human' => $n->created_at->diffForHumans(),
                'data' => $n->data,
            ];
        });

        return response()->json([
            'unread_count' => $unreadCount,
            'total_count' => $user->notifications()->count(),
            'notifications' => $formatted,
        ]);
    }

    /**
     * Stream notifikasi realtime menggunakan Server-Sent Events (SSE).
     */
    public function stream(Request $request): StreamedResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $lastEventId = $request->header('Last-Event-ID') ?? $request->query('last_id');

        return response()->stream(function () use ($user, $lastEventId) {
            // Tutup session lock agar request web lain dari user tidak terblokir
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }

            // Bersihkan semua buffer output agar data terkirim instan
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Instruksikan browser untuk retry setelah 3 detik jika koneksi terputus
            echo "retry: 3000\n\n";
            echo ": connected\n\n";
            flush();

            $startTime = time();
            $maxTime = 20; // 20 detik per siklus stream sebelum browser auto-reconnect
            $lastKnownId = $lastEventId;
            $lastKnownUnreadCount = null;

            while (time() - $startTime < $maxTime) {
                if (connection_aborted()) {
                    break;
                }

                // Cek notifikasi terbaru yang belum dibaca
                $latestNotification = $user->unreadNotifications()->latest()->first();
                $currentUnreadCount = $user->unreadNotifications()->count();

                if ($latestNotification && $latestNotification->id !== $lastKnownId) {
                    $lastKnownId = $latestNotification->id;
                    $lastKnownUnreadCount = $currentUnreadCount;

                    $payload = [
                        'id' => $latestNotification->id,
                        'unread_count' => $currentUnreadCount,
                        'total_count' => $user->notifications()->count(),
                        'data' => $latestNotification->data,
                        'created_at_human' => $latestNotification->created_at->diffForHumans(),
                    ];

                    echo "id: {$latestNotification->id}\n";
                    echo "event: notification\n";
                    echo 'data: '.json_encode($payload)."\n\n";
                    flush();
                } elseif ($lastKnownUnreadCount !== null && $currentUnreadCount !== $lastKnownUnreadCount) {
                    $lastKnownUnreadCount = $currentUnreadCount;

                    echo "event: unread_count\n";
                    echo 'data: '.json_encode([
                        'unread_count' => $currentUnreadCount,
                        'total_count' => $user->notifications()->count(),
                    ])."\n\n";
                    flush();
                }

                // Heartbeat ping agar koneksi tetap terjaga
                echo ": ping\n\n";
                flush();

                sleep(2);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai telah dibaca, dan redirect ke target jika diminta.
     */
    public function markAsRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();

            if ($request->has('redirect') || ! $request->wantsJson()) {
                $targetUrl = $notification->data['url'] ?? route('reports.index');

                return redirect($targetUrl);
            }
        }

        if (! $request->wantsJson()) {
            return redirect()->route('reports.index');
        }

        return response()->json([
            'success' => true,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Tandai semua notifikasi pengguna sebagai telah dibaca.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Hapus satu notifikasi milik pengguna.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $user->notifications()->where('id', $id)->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotifications()->count(),
                'total_count' => $user->notifications()->count(),
            ]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Hapus semua notifikasi milik pengguna (Clear All).
     */
    public function clearAll(Request $request): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $user->notifications()->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
                'total_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua notifikasi berhasil dibersihkan.');
    }
}
