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

    public function attendance_list() {
        return $this->hasMany('App\Attendance', 'student_information_id');
    }

    public function attendance() {
        return $this->belongsToMany('App\OfficeAccount', 'attendance', 'student_information_id', 'office_account_id')
                ->withPivot(['attendance_date', 'time_in_am', 'time_out_am', 'time_in_pm', 'time_out_pm', 'total_hours']);
    }

    public function office() {
        return $this->belongsToMany('App\OfficeDetail', 'ojt_office', 'student_information_id', 'office_detail_id')
                ->withPivot('duty_status')->withPivot('remarks');
    }

    public function qrcode() {
        return $this->hasOne('App\QrCode', 'student_information_id');
    }

    public function images()
    {
        return $this->morphMany('App\Images', 'imageable');
    }
}
