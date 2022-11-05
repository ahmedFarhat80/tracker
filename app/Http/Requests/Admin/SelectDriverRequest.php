<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SelectDriverRequest extends FormRequest
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
            'order_id'         => 'exists:orders,id',
            // 'driver_id'        => 'exists:drivers,id',
            'status'           => 'in:accepted,rejected,delivered',
            'reason'           => 'required_if:status,==,rejected',
        ];
    }
}
