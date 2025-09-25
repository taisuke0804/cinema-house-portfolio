<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreScreeningRequest extends FormRequest
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
            'movie_id' => ['required', 'integer', 'exists:movies,id'],
            'screening_date' => ['required', 'date', 'after_or_equal:tomorrow'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function attributes(): array
    {
        return [
            'screening_date' => '上映日',
            'start_time' => '上映開始時間',
            'end_time' => '上映終了時間',
        ];
    }

    public function messages(): array
    {
        return [
            'screening_date.after_or_equal' => '上映日には明日以降の日付を指定してください。',
            'end_time.after' => '上映終了時間は上映開始時間よりも後に設定してください。',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->start_time < '09:00' || $this->start_time > '21:00') {
                    $validator->errors()->add(
                        'start_time',
                        '上映開始時間は9:00から21:00の間で設定してください'
                    );
                }

                if ($this->end_time > '23:30') {
                    $validator->errors()->add(
                        'end_time',
                        '上映終了時間は23:30までに設定してください'
                    );
                }
            }
        ];
    }
}
