<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfficeDetail extends Model
{
    protected $table = 'office_details';

    public $timestamps = true;

    protected $fillable = [
        "office_registration_id","office_name","region","province","city","barangay","street"
    ];

    public function office() {
        return $this->belongsToMany('App\StudentInformation', 'office_details');
    }

    public function personnels()
    {
        return $this->hasMany('App\OfficeAccount', 'office_detail_id');
    }
}
