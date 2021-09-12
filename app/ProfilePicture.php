<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfilePicture extends Model
{
    protected $table = 'user_profile_picture';

    public $timestamps = true;

    protected $fillable = [        
        "image_path","image_type","image_name","status"
    ];

    public function account() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
