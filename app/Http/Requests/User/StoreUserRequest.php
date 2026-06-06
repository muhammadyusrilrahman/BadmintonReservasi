<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'string', Rule::in(['admin', 'kasir', 'staff', 'customer'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'nama lengkap',
            'email'    => 'alamat email',
            'phone'    => 'nomor telepon',
            'password' => 'kata sandi',
            'role'     => 'peran',
        ];
    }
}
