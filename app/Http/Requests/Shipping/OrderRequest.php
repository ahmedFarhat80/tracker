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
            'restaurant_id'     => 'required_if:receipt_type,restaurant|nullable|exists:restaurants,id',
            'order_no'          => 'required|unique:orders,order_no,'.$this->id,
            'from_client_name'       => 'required|string|max:255',
            'client_name'       => 'required|string|max:255',
            'origin_address'           => 'required',
            'origin_lat'               => 'required|numeric',
            'origin_lng'               => 'required|numeric',
            'destination_address'           => 'required',
            'destination_lat'               => 'required|numeric',
            'destination_lng'               => 'required|numeric',
            'from_details'           => 'required',
            'details'           => 'required',
            'price'             => 'required',
            'duration'          => 'required|numeric',
            'distance'          => 'required|numeric',
            'paid_status'       => 'required|in:cash,visa,link',
            'fare'              => 'required',
            'totalWithFare'     => 'required',

            'street'            => 'sometimes',
            'building'          => 'sometimes',
            'floor'             => 'sometimes',
            'flat'              => 'sometimes',
            'from_flat_type'         => 'in:flat,house,office',
            'flat_type'         => 'in:flat,house,office',
            'receipt_type'         => 'in:visitor,restaurant',

            'from_mobile'            => [
                'required',
                // 'regex:/(01)[0-9]{9}/',
                // 'size:11'
            ],'mobile'            => [
                'required',
                // 'regex:/(01)[0-9]{9}/',
                // 'size:11'
            ],
        ];
    }
}
