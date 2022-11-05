<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
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
            'en_title'          => 'required|string|max:255|unique:currencies,en_title,'.$this->id,
            'ar_title'          => 'required|string|max:255|unique:currencies,ar_title,'.$this->id,
            'symbol'            => 'required|min:2|max:3|unique:currencies,symbol,'.$this->id,
            'exchange_rate'     => 'required',
            'sequence'          => 'required|numeric',
            'status'            => 'boolean',
            
        ];
    }
}
