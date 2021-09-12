<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    protected $table = 'user_information';

    public $timestamps = true;

    protected $fillable = [
        "clinic_user_id","firstname","middlename","lastname","birthday","contact","address"
    ];

    public function account() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
