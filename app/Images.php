<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $table = 'images';

    public $timestamps = true;

    protected $fillable = [        
        "image_path","image_type","image_name","status", "set_as"
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
