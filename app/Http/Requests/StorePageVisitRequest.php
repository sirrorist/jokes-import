<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePageVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'url' => ['required', 'string', 'max:2048'],
            'visitor_id' => ['sometimes', 'nullable', 'string', 'max:64'],
            'device_type' => ['sometimes', 'nullable', 'string', 'max:32'],
            'visited_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
