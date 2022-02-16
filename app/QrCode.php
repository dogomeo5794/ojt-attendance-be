<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $table = 'generated_qrcode';

    public $timestamps = true;

    protected $fillable = [
        "qrcode_url", "student_information_id"
    ];

    public function student() {
        return $this->belongsTo('App\StudentInformation', 'student_information_id');
    }

    public function images()
    {
        return $this->morphMany('App\Images', 'imageable');
    }
}
