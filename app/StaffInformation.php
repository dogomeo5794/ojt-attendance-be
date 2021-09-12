<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffInformation extends Model
{
    protected $table = 'staff_information';

    public $timestamps = true;

    protected $fillable = [
        "company_id","firstname","middlename","lastname","birthday","contact","address"
    ];

    public function account() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
