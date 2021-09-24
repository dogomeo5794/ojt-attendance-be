<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfficeAccount extends Model
{
    protected $table = 'office_account';

    public $timestamps = true;

    protected $fillable = [
        "company_id","first_name","middle_name","last_name","birthday","contact_no","user_id", "office_detail_id"
    ];

    public function attendance() {
        return $this->belongsToMany('App\StudentInformation', 'attendance');
    }

    public function account() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function office_details() {
        return $this->belongsTo('App\OfficeDetails', 'office_detail_id');
    }

    public function account_info()
    {
        return $this->morphOne('App\User', 'user_info');
    }

    public function images()
    {
        return $this->morphMany('App\Images', 'imageable');
    }
}
