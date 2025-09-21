<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Genre;
use Illuminate\Validation\Rule;

class MovieSearchRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'genre' => ['nullable', 'integer', Rule::in(array_column(Genre::cases(), 'value'))],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
