<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'order_no'          => 'required|unique:orders,order_no,' . $this->id,
            'client_name'       => 'required|string|max:255',
            'address'           => 'required',
            'lon'               => 'required|numeric',
            'lat'               => 'required|numeric',
            'details'           => 'required',
            'price'             => 'required',
            'duration'          => 'required',
            'distance'          => 'required',
            'fare'              => 'required',
            'totalWithFare'     => 'required',
            'paid_status'       => 'required|in:cash,visa',
            // 'street'            => 'required',
            // 'building'          => 'required',
            // 'floor'             => 'required',
            // 'flat'              => 'required',
            'flat_type'         => 'in:flat,house,office',
            'mobile'            => [
                'required',
                // 'regex:/(01)[0-9]{9}/',
                // 'size:11'
            ],
            

            
        ];
    }
}
