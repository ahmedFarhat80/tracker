<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $primarykey = 'id'; 

	protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'radius',
        'time_from',
        'time_to',
        'distance_type' // Km,Mi,Me
    ];

    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id');
    }
}
