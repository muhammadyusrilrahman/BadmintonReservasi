<?php

namespace App\Http\Requests\Reservation;

use App\Http\Requests\BaseFormRequest;

class StoreAdminReservationRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'user_id'        => ['required', 'exists:users,id'],
            'court_id'       => ['required', 'exists:courts,id'],
            'date'           => ['required', 'date', 'after_or_equal:today'],
            'start_time'     => ['required', 'date_format:H:i'],
            'duration_hours' => ['required', 'integer', 'min:1', 'max:5'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'        => 'pelanggan',
            'court_id'       => 'lapangan',
            'date'           => 'tanggal',
            'start_time'     => 'waktu mulai',
            'duration_hours' => 'durasi (jam)',
            'notes'          => 'catatan',
        ];
    }
}
