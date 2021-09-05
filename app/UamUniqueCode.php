<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UamUniqueCode extends Model
{
    protected $table = 'uam_unique_code';

    public $timestamps = true;

    protected $fillable = [
        'unique_code', 'user_system_id', 'status'
    ];
}
