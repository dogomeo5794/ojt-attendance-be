<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentInformation extends Model
{
    protected $table = 'student_information';

    public $timestamps = true;

    protected $fillable = [
        "school_id",
        "email",
        "region",
        "province",
        "city",
        "barangay",
        "street",
        "first_name",
        "middle_name",
        "last_name",
        "birthday",
        "contact_no",
        "course_code",
        "course_name",
        "section",
        "year_level",
    ];

    public function attendance() {
        return $this->belongsToMany('App\OfficeAccount', 'attendance');
    }

    public function office() {
        return $this->belongsToMany('App\OfficeDetails', 'office_details');
    }

    public function qrcode() {
        return $this->hasOne('App\QrCode', 'student_information_id');
    }

    public function images()
    {
        return $this->morphMany('App\Images', 'imageable');
    }
}
