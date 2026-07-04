<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Tampilkan form tulis ulasan (baru).
     */
    public function create(Reservation $reservation)
    {
        abort_if($reservation->user_id !== Auth::id(), 403);

        if (! Review::canReview($reservation)) {
            return redirect()
                ->route('customer.reservations.show', $reservation)
                ->with('error', 'Ulasan hanya bisa ditulis 1 hari setelah reservasi selesai dan belum pernah memberikan ulasan.');
        }

        $reservation->load('court');

        return view('customer.reviews.create', compact('reservation'));
    }

    /**
     * Simpan ulasan baru.
     */
    public function store(Request $request, Reservation $reservation)
    {
        abort_if($reservation->user_id !== Auth::id(), 403);

        if (! Review::canReview($reservation)) {
            return redirect()
                ->route('customer.reservations.show', $reservation)
                ->with('error', 'Tidak dapat memberikan ulasan untuk reservasi ini.');
        }

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'reservation_id' => $reservation->id,
            'user_id'        => Auth::id(),
            'court_id'       => $reservation->court_id,
            'rating'         => $validated['rating'],
            'comment'        => $validated['comment'] ?? null,
            'is_hidden'      => false,
        ]);

        return redirect()
            ->route('customer.reservations.show', $reservation)
            ->with('success', 'Terima kasih! Ulasan Anda berhasil disimpan.');
    }

    /**
     * Tampilkan form edit ulasan.
     */
    public function edit(Reservation $reservation)
    {
        abort_if($reservation->user_id !== Auth::id(), 403);

        $review = $reservation->review;
        abort_if(! $review, 404);
        abort_if($review->user_id !== Auth::id(), 403);

        $reservation->load('court');

        return view('customer.reviews.edit', compact('reservation', 'review'));
    }

    /**
     * Update ulasan yang sudah ada.
     */
    public function update(Request $request, Reservation $reservation)
    {
        abort_if($reservation->user_id !== Auth::id(), 403);

        $review = $reservation->review;
        abort_if(! $review, 404);
        abort_if($review->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Reset balasan admin jika rating/komentar berubah
        $review->update([
            'rating'         => $validated['rating'],
            'comment'        => $validated['comment'] ?? null,
            'admin_reply'    => null,
            'admin_reply_at' => null,
        ]);

        return redirect()
            ->route('customer.reservations.show', $reservation)
            ->with('success', 'Ulasan Anda berhasil diperbarui.');
    }

    /**
     * Hapus ulasan milik customer.
     */
    public function destroy(Reservation $reservation)
    {
        abort_if($reservation->user_id !== Auth::id(), 403);

        $review = $reservation->review;
        abort_if(! $review, 404);
        abort_if($review->user_id !== Auth::id(), 403);

        $review->delete();

        return redirect()
            ->route('customer.reservations.show', $reservation)
            ->with('success', 'Ulasan berhasil dihapus.');
    }
}
