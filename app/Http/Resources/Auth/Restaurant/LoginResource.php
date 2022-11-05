<?php

namespace App\Http\Resources\Auth\Restaurant;

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
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        $authToken = $this->authToken;
        
        return [
            'restaurant'=>[
                
                'id'                => $this->id,
                'user_id'                => $this->user_id,
                //'name'              => $this->{app()->getLocale().'_name'},
                'en_name'           =>$this->en_name,
                'ar_name'           =>$this->ar_name,
                'email'             => $this->email,
                'address'           => $this->address,
                'mobile'            => $this->mobile,
                'lon'               => $this->lon,
                'lat'               => $this->lat,
                'status'            => $this->status,
                'note'              => $this->note,
                'photo'             => $this->photo,
            ],

            'shipping' => $this->user,
            'token' => $authToken,
        ];
    }
}
