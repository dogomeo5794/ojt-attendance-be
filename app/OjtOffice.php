<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OjtOffice extends Model
{
    protected $table = 'ojt_office';

    public $timestamps = true;

    protected $fillable = [
        'student_information_id', 'office_detail_id'
    ];
}
