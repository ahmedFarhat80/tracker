<?php

namespace App\Http\Resources\Auth\User;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{

    /**
     * @var
     */
    private $authToken;


    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $authToken)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);

        $this->resource     = $resource;
        $this->authToken    = $authToken;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $authToken = $this->authToken;
        
        return [
            'shipping'=>[
                
                'id'                => $this->id,
                'quote_id'          => $this->quote_id,
                //'name'              => $this->{app()->getLocale().'_name'},
                'en_name'           =>$this->en_name,
                'ar_name'           =>$this->ar_name,
                'email'             => $this->email,
                'address'           => $this->address,
                'mobile'            => $this->mobile,
                'lon'               => $this->lon,
                'lat'               => $this->lat,
                'account_name'      => $this->account_name,
                'swift_code'        => $this->swift_code,
                'iban'              => $this->iban,
                'SubMerchUID'       => $this->SubMerchUID,
                'status'            => $this->status,
                'photo'             => $this->photo,
            ],
            'drivers'   => $this->drivers,
            'fare'      => $this->fare,
            'token'     => $authToken,
        ];
    }
}
