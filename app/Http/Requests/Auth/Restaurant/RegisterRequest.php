<?php

namespace App\Http\Requests\Auth\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'user_id'       => 'exists:users,id',
            'quote_id'      => 'exists:quotes,id',
            'en_name'       => 'required|string|max:255',
            'ar_name'       => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:restaurants',
            'password'      => 'required|string|min:6|confirmed',
            'photo'         => 'mimes:jpg,jpeg,png',
            'address'       => 'required',
            'lon'           => 'required|numeric',
            'lat'           => 'required|numeric',
            'mobile'        => [
                'required',
                'regex:/(01)[0-9]{9}/',
                "unique:restaurants",
                // 'size:11'
            ],
            'telephone'        => [
                // 'required',
                'regex:/(01)[0-9]{9}/',
                "unique:users",
                // 'size:11'
            ],
        ];
    }
}
