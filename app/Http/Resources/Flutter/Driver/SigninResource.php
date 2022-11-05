<?php

namespace App\Http\Resources\Flutter\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class SigninResource extends JsonResource
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
        $this->resource = $resource;
        $this->authToken = $authToken;
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
            'driver'=>[
                'id'        => $this->id,
                'name'      => $this->{app()->getLocale().'_name'},
                'email'     => $this->email,
                'mobile'    => $this->mobile,
                'photo'     => $this->photo,
            ],

            'shipping'=>$this->users,
            'token'=>$authToken,

        ];
    }
}
