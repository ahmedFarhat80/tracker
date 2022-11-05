<?php

namespace App\Http\Requests\Flutter\Driver;

use Illuminate\Foundation\Http\FormRequest;

class SigninRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mobile'            => [
                'required',
                'digits:8',
                'numeric',
                // 'regex:/(01)[0-9]{9}/',
                'exists:drivers,mobile',
                // 'size:11'
            ],
            'password' => ['required', 'string', 'min:6'],
            
        ];
    }
}
