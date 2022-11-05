<?php

namespace App\Http\Requests\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            // 'user_id'           => 'required|exists:users,id',
            'radius'            => 'required|numeric',
            'time_from'         => 'required|date_format:H:i',
            'time_to'           => 'required|date_format:H:i|after:time_from',
            'distance_type'     => 'required|in:km,miles,meter',
        ];
    }
}
