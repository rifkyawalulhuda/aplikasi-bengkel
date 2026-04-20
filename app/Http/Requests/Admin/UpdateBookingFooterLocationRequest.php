<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingFooterLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'footer_address' => ['required', 'string', 'max:255'],
            'footer_latitude' => ['required', 'numeric', 'between:-90,90'],
            'footer_longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'footer_address.required' => 'Alamat footer bengkel wajib diisi.',
            'footer_latitude.required' => 'Latitude footer bengkel wajib diisi.',
            'footer_longitude.required' => 'Longitude footer bengkel wajib diisi.',
        ];
    }
}
