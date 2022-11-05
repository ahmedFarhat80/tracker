<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentInfoRequest extends FormRequest
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
            'MerchUID'  => 'required',
            'SubMerchUID'  => 'required',
            'account_name'  => 'required',
            'swift_code'  => 'required',
            'iban'  => 'required',
            'secretKey'  => 'required',
            
            
            
        ];
    }
}
