<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Seed review data for completed reservations.
     */
    public function run(): void
    {
        $completedReservations = Reservation::where('status', 'completed')
            ->whereDoesntHave('review')
            ->with('user', 'court')
            ->inRandomOrder()
            ->take(15)
            ->get();

        $comments = [
            5 => [
                'Lapangan bersih dan terawat, sangat nyaman bermain di sini!',
                'Fasilitas lengkap, pencahayaan bagus. Pasti akan datang lagi.',
                'Lantai tidak licin, cocok untuk bermain kompetitif. Top!',
                'Pelayanan ramah, booking mudah. Recommended banget!',
                'Tempat favorit untuk latihan badminton. Kualitas lapangan premium.',
            ],
            4 => [
                'Bagus secara keseluruhan, hanya AC agak kurang dingin.',
                'Lapangan oke, parkiran agak sempit tapi overall puas.',
                'Net dan garis lapangan bagus, fasilitas pendukung memadai.',
                'Booking online cepat dan mudah, lapangan sesuai ekspektasi.',
            ],
            3 => [
                'Lapangan cukup baik, tapi pencahayaan bisa ditingkatkan.',
                'Standar sih, tidak ada yang istimewa tapi juga tidak buruk.',
                'Lumayan untuk latihan biasa, mungkin perlu renovasi kecil.',
            ],
            2 => [
                'Agak kotor di area pinggir, perlu dibersihkan lebih rutin.',
                'Lantai ada yang sedikit retak, perlu perbaikan.',
            ],
            1 => [
                'Sangat mengecewakan, AC mati dan lapangan belum disapu.',
            ],
        ];

        foreach ($completedReservations as $reservation) {
            // Weighted random: more 4-5 stars
            $rating = $this->weightedRating();

            $availableComments = $comments[$rating] ?? ['Tidak ada komentar.'];
            $comment = $availableComments[array_rand($availableComments)];

            // 20% chance of no comment
            if (rand(1, 5) === 1) {
                $comment = null;
            }

            Review::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $reservation->user_id,
                'court_id'       => $reservation->court_id,
                'rating'         => $rating,
                'comment'        => $comment,
                'created_at'     => $reservation->date->addHours(rand(18, 22)),
            ]);
        }
    }

    private function weightedRating(): int
    {
        $weights = [1 => 2, 2 => 5, 3 => 13, 4 => 35, 5 => 45];
        $roll = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $rating => $weight) {
            $cumulative += $weight;
            if ($roll <= $cumulative) {
                return $rating;
            }
        }

        return 4;
    }
}
