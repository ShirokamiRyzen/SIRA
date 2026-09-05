<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Tambahkan komentar baru atau balasan (nested reply).
     */
    public function store(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1500'],
            'parent_id' => ['nullable', 'exists:report_comments,id'],
        ]);

        // Jika balasan (reply), pastikan komentar induk berada pada laporan yang sama
        if (! empty($validated['parent_id'])) {
            $parent = ReportComment::findOrFail($validated['parent_id']);
            if ($parent->report_id !== $report->id) {
                return back()->withErrors(['content' => 'Induk komentar tidak valid.']);
            }
        }

        ReportComment::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Komentar Anda berhasil diposting!');
    }

    /**
     * Hapus komentar (hanya oleh pemilik komentar).
     */
    public function destroy(ReportComment $comment): RedirectResponse
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus komentar ini.');
        }

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
