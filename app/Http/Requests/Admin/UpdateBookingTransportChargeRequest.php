<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingTransportChargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transport_free_radius_km' => ['required', 'numeric', 'min:0'],
            'transport_fee_per_km' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'transport_free_radius_km.required' => 'Radius gratis wajib diisi.',
            'transport_free_radius_km.numeric' => 'Radius gratis harus berupa angka.',
            'transport_free_radius_km.min' => 'Radius gratis tidak boleh kurang dari 0.',
            'transport_fee_per_km.required' => 'Biaya transport per km wajib diisi.',
            'transport_fee_per_km.integer' => 'Biaya transport per km harus berupa angka bulat.',
            'transport_fee_per_km.min' => 'Biaya transport per km tidak boleh kurang dari 0.',
        ];
    }

    public function attributes(): array
    {
        return [
            'transport_free_radius_km' => 'radius gratis',
            'transport_fee_per_km' => 'biaya transport per km',
        ];
    }
}
