<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
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
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:admins,email,'.\Auth::guard('admin-api')->id(),
            'password'          => 'required_without:id|string|min:6',
            'status'            => 'boolean',
            'photo'             => 'mimes:jpg,jpeg,png',
        ];
    }
}
