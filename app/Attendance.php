<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    public $timestamps = true;

    protected $fillable = [
        "attendance_date",
        "time_in_am",
        "time_out_am",
        "time_in_pm",
        "time_out_pm",
        "total_hours",
        "office_account_id",
        "student_information_id",
    ];
}
