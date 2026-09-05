<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportComment;
use App\Models\User;
use App\Notifications\CommentNotification;
use App\Services\AiSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    /**
     * Tambahkan komentar baru atau balasan (nested reply), dengan dukungan AJAX dan bot @Sira.
     */
    public function store(Request $request, Report $report, AiSummaryService $aiService): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1500'],
            'parent_id' => ['nullable', 'exists:report_comments,id'],
        ]);

        // Jika balasan (reply), pastikan komentar induk berada pada laporan yang sama
        if (! empty($validated['parent_id'])) {
            $parent = ReportComment::findOrFail($validated['parent_id']);
            if ($parent->report_id !== $report->id) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Induk komentar tidak valid.',
                    ], 422);
                }

                return back()->withErrors(['content' => 'Induk komentar tidak valid.']);
            }
        }

        $comment = ReportComment::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
        ]);

        $comment->load(['user', 'replies.user']);

        // Kirim notifikasi mention dan balasan
        $this->dispatchCommentNotifications($report, $comment, $validated['parent_id'] ?? null);

        $hasAiMention = $aiService->isAiMentioned($validated['content']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar Anda berhasil diposting!',
                'comment_id' => $comment->id,
                'parent_id' => $comment->parent_id,
                'has_ai_mention' => $hasAiMention,
                'comments_count' => $report->fresh()->comments_count,
                'comment_html' => view('reports._comment_item', ['comment' => $comment])->render(),
            ]);
        }

        // Fallback untuk synchronous submission non-AJAX
        if ($hasAiMention) {
            $aiComment = $aiService->generateAiResponse($report, $comment);
            if ($aiComment) {
                $aiComment->load(['user', 'replies.user']);
                if ($comment->user && strtolower($comment->user->username) !== 'sira') {
                    Notification::send($comment->user, new CommentNotification(
                        type: 'reply',
                        senderUsername: 'Sira',
                        senderName: 'SIRA AI Assistant',
                        reportId: $report->id,
                        reportTitle: $report->title,
                        commentId: $aiComment->id,
                        snippet: Str::limit($aiComment->content, 80),
                    ));
                }
            }
        }

        return back()->with('success', 'Komentar Anda berhasil diposting!');
    }

    /**
     * Generate balasan otomatis dari AI @Sira secara asynchronous / bertahap.
     */
    public function generateAiReply(Request $request, Report $report, ReportComment $comment, AiSummaryService $aiService): JsonResponse
    {
        if ($comment->report_id !== $report->id) {
            return response()->json([
                'success' => false,
                'message' => 'Komentar tidak sesuai dengan laporan yang dimaksud.',
            ], 422);
        }

        $aiComment = $aiService->generateAiResponse($report, $comment);

        if (! $aiComment) {
            return response()->json([
                'success' => false,
                'message' => 'AI @Sira tidak dapat menghasilkan balasan saat ini.',
            ], 500);
        }

        $aiComment->load(['user', 'replies.user']);

        // Kirim notifikasi balasan AI ke pembuat komentar asli
        if ($comment->user && strtolower($comment->user->username) !== 'sira') {
            Notification::send($comment->user, new CommentNotification(
                type: 'reply',
                senderUsername: 'Sira',
                senderName: 'SIRA AI Assistant',
                reportId: $report->id,
                reportTitle: $report->title,
                commentId: $aiComment->id,
                snippet: Str::limit($aiComment->content, 80),
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Balasan AI @Sira berhasil dibuat.',
            'ai_comment_id' => $aiComment->id,
            'comments_count' => $report->fresh()->comments_count,
            'ai_comment_html' => view('reports._comment_item', ['comment' => $aiComment])->render(),
        ]);
    }

    /**
     * Kirim notifikasi balasan dan mention ke pengguna terkait.
     */
    protected function dispatchCommentNotifications(Report $report, ReportComment $comment, ?int $parentId = null): void
    {
        $sender = Auth::user();
        $senderUsername = $sender ? $sender->username : 'anon';
        $senderName = $sender ? $sender->name : 'Warga';
        $senderId = $sender ? $sender->id : null;

        $notifiedUserIds = [];

        // 1. Notifikasi Balasan (Reply)
        if ($parentId) {
            $parent = ReportComment::with('user')->find($parentId);
            if ($parent && $parent->user && $parent->user_id !== $senderId) {
                Notification::send($parent->user, new CommentNotification(
                    type: 'reply',
                    senderUsername: $senderUsername,
                    senderName: $senderName,
                    reportId: $report->id,
                    reportTitle: $report->title,
                    commentId: $comment->id,
                    snippet: Str::limit($comment->content, 80),
                ));
                $notifiedUserIds[] = $parent->user_id;
            }
        }

        // 2. Notifikasi Mention (@username)
        if (preg_match_all('/@([a-zA-Z0-9_]{3,30})/', $comment->content, $matches)) {
            $mentionedUsernames = array_unique($matches[1]);
            $mentionedUsers = User::whereIn('username', $mentionedUsernames)
                ->when($senderId, fn ($q) => $q->where('id', '!=', $senderId))
                ->whereNotIn('id', $notifiedUserIds)
                ->whereRaw('LOWER(username) != ?', ['sira'])
                ->get();

            if ($mentionedUsers->isNotEmpty()) {
                Notification::send($mentionedUsers, new CommentNotification(
                    type: 'mention',
                    senderUsername: $senderUsername,
                    senderName: $senderName,
                    reportId: $report->id,
                    reportTitle: $report->title,
                    commentId: $comment->id,
                    snippet: Str::limit($comment->content, 80),
                ));
            }
        }
    }

    /**
     * Hapus komentar (hanya oleh pemilik komentar).
     */
    public function destroy(Request $request, ReportComment $comment): JsonResponse|RedirectResponse
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus komentar ini.');
        }

        $report = $comment->report;
        $comment->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil dihapus.',
                'comments_count' => $report ? $report->fresh()->comments_count : 0,
            ]);
        }

        return back()->with('success', 'Komentar berhasil dihapus.');
    }

    /**
     * Cari saran akun pengguna untuk fitur auto-complete mention (@).
     * Mengetik @ saja atau kurang dari 3 karakter hanya memunculkan bot Sira AI (jika cocok).
     * Pengguna lain baru dimunculkan jika sudah mengetik minimal 3 karakter username/nama.
     */
    public function mentionSuggestions(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));
        $qLower = strtolower($q);

        $users = collect();

        // 1. Cek apakah bot Sira cocok (saat @ saja atau @s, @si, @sira)
        $siraUser = User::whereRaw('LOWER(username) = ?', ['sira'])->first();
        $isSiraMatched = $siraUser && (
            $q === '' ||
            str_contains(strtolower($siraUser->username), $qLower) ||
            str_contains(strtolower($siraUser->name), $qLower)
        );

        if ($isSiraMatched) {
            $users->push($siraUser);
        }

        // 2. Pengguna lain hanya dimunculkan jika mengetik minimal 3 karakter
        if (mb_strlen($q) >= 3) {
            $otherUsers = User::query()
                ->select(['id', 'name', 'username'])
                ->whereRaw('LOWER(username) != ?', ['sira'])
                ->where(function ($sub) use ($q) {
                    $sub->where('username', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%");
                })
                ->take(15)
                ->get();

            // Urutkan prioritas: yang berawalan dengan query ditaruh paling atas
            $otherUsers = $otherUsers->sortByDesc(function ($u) use ($qLower) {
                if (str_starts_with(strtolower($u->username), $qLower)) {
                    return 2;
                }
                if (str_starts_with(strtolower($u->name), $qLower)) {
                    return 1;
                }

                return 0;
            })->values();

            foreach ($otherUsers as $ou) {
                $users->push($ou);
            }
        }

        $results = $users->take(6)->map(function ($u) {
            $isAi = strtolower($u->username) === 'sira';

            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username,
                'is_ai' => $isAi,
                'badge' => $isAi ? 'SIRA AI' : null,
            ];
        });

        return response()->json([
            'users' => $results,
        ]);
    }
}
