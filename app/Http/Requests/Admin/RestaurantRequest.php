<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantRequest extends FormRequest
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
            'user_id'          => 'exists:users,id',
            'quote_id'         => 'exists:quotes,id',
            'en_name'          => 'required|string|max:255',
            'ar_name'          => 'required|string|max:255',
            'email'            => 'required|email|unique:restaurants,email,'.$this->id,
            'password'         => 'required_without:id|string|min:6',
            'status'           => 'boolean',
            'photo'            => 'mimes:jpg,jpeg,png',
            'address'          => 'required|string|max:255',
            'lon'              => 'required|numeric',
            'lat'              => 'required|numeric',
            'mobile'           => [
                'required',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                "unique:restaurants,mobile,".$this->id,
                'digits:8',
                'numeric',
                // 'size:11'
            ],
            'telephone'           => [
                // 'required',
                'numeric',
                "unique:restaurants,telephone,".$this->id,
                // 'size:11'
            ],

        ];
    }
}
