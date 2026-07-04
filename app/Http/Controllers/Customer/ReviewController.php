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
     * Tampilkan form tulis ulasan.
     */
    public function create(Reservation $reservation)
    {
        // Pastikan reservasi milik customer yang login
        abort_if($reservation->user_id !== Auth::id(), 403);

        // Cek apakah boleh review
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
}
