<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReserveSeatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'screening_id' => ['required', 'integer', 'exists:screenings,id'],
            // 複数席（最大3）
            'seat_ids' => ['required', 'array', 'min:1', 'max:3'],
            'seat_ids.*' => ['required', 'integer', 'distinct', 'exists:seats,id'],
        ];
    }
}
