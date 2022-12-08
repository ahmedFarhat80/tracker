<?php

namespace App\Http\Requests\Shipping;

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
            'restaurant_id'     => 'required_if:receipt_type,restaurant|exists:restaurants,id',
            'order_no'          => 'required|unique:orders,order_no,'.$this->id,
            'client_name'       => 'required|string|max:255',
            'origin_address'           => 'required',
            'origin_lat'               => 'required|numeric',
            'origin_lng'               => 'required|numeric',
            'destination_address'           => 'required',
            'destination_lat'               => 'required|numeric',
            'destination_lng'               => 'required|numeric',
            'details'           => 'required',
            'price'             => 'required',
            'duration'          => 'required',
            'distance'          => 'required',
            'paid_status'       => 'required|in:cash,visa,link',
            'fare'              => 'required',
            'totalWithFare'     => 'required',

            'street'            => 'sometimes',
            'building'          => 'sometimes',
            'floor'             => 'sometimes',
            'flat'              => 'sometimes',
            'flat_type'         => 'in:flat,house,office',
            'receipt_type'         => 'in:visitor,restaurant',

            'mobile'            => [
                'required',
                // 'regex:/(01)[0-9]{9}/',
                // 'size:11'
            ],
        ];
    }
}
