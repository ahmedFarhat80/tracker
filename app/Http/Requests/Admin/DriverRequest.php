<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest
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
            'en_name'           => 'required|string|max:255|unique:drivers,en_name,' . $this->id,
            'ar_name'           => 'required|string|max:255|unique:drivers,ar_name,' . $this->id,
            'email'             => 'required|email|unique:drivers,email,' . $this->id,
            'user_ids'          => 'array',
            'user_ids.*'        => 'exists:users,id', // check each item in the array
            'photo'             => 'mimes:jpg,jpeg,png',
            'lon'               => 'numeric',
            'lat'               => 'numeric',
            'mobile'            => [
                'required',
                'unique:drivers,mobile,' . $this->id,
                'digits:8',
                'numeric',
                // 'regex:/(01)[0-9]{9}/',
                // 'size:11'
            ],
            // 'password'          => 'required_without:id|string|min:6',

            
        ];
    }
}
