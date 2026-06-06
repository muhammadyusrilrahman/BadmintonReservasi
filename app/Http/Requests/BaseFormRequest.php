<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt for API requests.
     * For web requests, Laravel's default behavior (redirect back) is used.
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }

    /**
     * Get custom validation messages in Bahasa Indonesia.
     */
    public function messages(): array
    {
        return array_merge($this->defaultMessages(), $this->customMessages());
    }

    /**
     * Default validation messages in Bahasa Indonesia.
     */
    protected function defaultMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'email' => ':attribute harus berupa alamat email yang valid.',
            'unique' => ':attribute sudah digunakan.',
            'min' => ':attribute minimal :min karakter.',
            'max' => ':attribute maksimal :max karakter.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'exists' => ':attribute tidak ditemukan.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
            'image' => ':attribute harus berupa file gambar.',
            'mimes' => ':attribute harus berupa file: :values.',
            'in' => ':attribute yang dipilih tidak valid.',
            'between' => ':attribute harus antara :min dan :max.',
            'after' => ':attribute harus setelah :date.',
            'before' => ':attribute harus sebelum :date.',
            'after_or_equal' => ':attribute harus setelah atau sama dengan :date.',
        ];
    }

    /**
     * Override in child classes for custom messages.
     */
    protected function customMessages(): array
    {
        return [];
    }

    // ──────────────────────────────────────
    // Common validation rule helpers
    // ──────────────────────────────────────

    /**
     * Phone number validation rules (Indonesian format).
     */
    protected function phoneRules(bool $required = true): array
    {
        $rules = ['string', 'min:10', 'max:15', 'regex:/^(\+62|62|0)[0-9]{8,13}$/'];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Date validation rules.
     */
    protected function dateRules(bool $required = true, ?string $after = null): array
    {
        $rules = ['date'];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        if ($after) {
            $rules[] = "after:{$after}";
        }

        return $rules;
    }

    /**
     * Price/currency validation rules.
     */
    protected function priceRules(bool $required = true): array
    {
        $rules = ['numeric', 'min:0'];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    /**
     * Image upload validation rules.
     */
    protected function imageRules(bool $required = true, int $maxKb = 2048): array
    {
        $rules = ['image', 'mimes:jpg,jpeg,png,webp', "max:{$maxKb}"];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }
}
