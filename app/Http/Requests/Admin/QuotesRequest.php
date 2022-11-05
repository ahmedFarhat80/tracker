<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class QuotesRequest extends FormRequest
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
            'en_title'          => 'required|string|max:255|unique:quotes,en_title,'.$this->id,
            'ar_title'          => 'required|string|max:255|unique:quotes,ar_title,'.$this->id,
            'cost'              => 'required|regex:/^(\d+(,\d{1,2})?)?$/',
            'drivers_count'     => 'required|numeric',
            'sequence'          => 'required|numeric',
            'months'            => 'required|numeric',
            'status'            => 'boolean',
            
        ];
    }
}
