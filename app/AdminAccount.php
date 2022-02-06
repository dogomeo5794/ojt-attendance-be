<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminAccount extends Model
{
    protected $table = 'admin_account';

    public $timestamps = true;

    protected $fillable = [
        "company_id", "region", "province", "city", "barangay", "street", "first_name", "middle_name",
        "last_name", "birthday", "contact_no", "user_id"
    ];

    public function account()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function account_info()
    {
        return $this->morphOne('App\User', 'user_info');
    }
}
