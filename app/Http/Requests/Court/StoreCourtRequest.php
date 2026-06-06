<?php

namespace App\Http\Requests\Court;

use App\Http\Requests\BaseFormRequest;

class StoreCourtRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100', 'unique:courts,name'],
            'type'           => ['required', 'in:rubber,synthetic,wood'],
            'description'    => ['nullable', 'string', 'max:500'],
            'price_per_hour' => ['nullable', 'integer', 'min:0'],
            'photo'          => $this->imageRules(false, 2048),
            'is_active'      => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'           => 'nama lapangan',
            'type'           => 'jenis lapangan',
            'description'    => 'deskripsi',
            'price_per_hour' => 'harga per jam',
            'photo'          => 'foto lapangan',
            'is_active'      => 'status aktif',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? true : false,
        ]);
    }
}
