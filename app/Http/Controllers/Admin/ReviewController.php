<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Daftar semua review dengan filter.
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'court', 'reservation'])
            ->latest();

        // Filter lapangan
        if ($request->filled('court_id')) {
            $query->where('court_id', $request->court_id);
        }

        // Filter rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter status sembunyikan
        if ($request->filled('status')) {
            if ($request->status === 'hidden') {
                $query->where('is_hidden', true);
            } elseif ($request->status === 'visible') {
                $query->where('is_hidden', false);
            }
        }

        $reviews = $query->paginate(15)->withQueryString();
        $courts  = Court::active()->orderBy('name')->get();

        // Statistik
        $stats = [
            'total'   => Review::count(),
            'average' => round(Review::avg('rating'), 1),
            'hidden'  => Review::where('is_hidden', true)->count(),
            'replied' => Review::whereNotNull('admin_reply')->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'courts', 'stats'));
    }

    /**
     * Simpan balasan admin.
     */
    public function reply(Request $request, Review $review)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ]);

        $review->update([
            'admin_reply'    => $request->admin_reply,
            'admin_reply_at' => now(),
        ]);

        return back()->with('success', 'Balasan berhasil disimpan.');
    }

    /**
     * Toggle sembunyikan / tampilkan review.
     */
    public function toggleHidden(Review $review)
    {
        $review->update(['is_hidden' => ! $review->is_hidden]);

        $msg = $review->is_hidden
            ? 'Ulasan berhasil disembunyikan dari publik.'
            : 'Ulasan berhasil ditampilkan kembali.';

        return back()->with('success', $msg);
    }

    /**
     * Hapus review.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
