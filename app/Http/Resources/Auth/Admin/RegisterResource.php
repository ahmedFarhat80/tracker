<?php

namespace App\Http\Resources\Auth\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
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
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        $authToken = $this->authToken;
        return [
            'user'=>[
                'id'                =>  $this->id,
                'name'              =>  $this->name,
                'email'             =>  $this->email,
                'status'            =>  $this->status,
                'email_verified_at' =>  $this->email_verified_at,
                // 'role_id'=>$this->role_id,
                
            ],

            'token'=>$authToken,
        ];
    }
}
