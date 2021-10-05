<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountEvaluated extends Model
{
    protected $table = 'evaluated_account';

    public $timestamps = true;	

    protected $fillable = [
        "action_perform_date","action_perform","remarks","office_account_id","admin_account_id","created_at","updated_at"
    ];

    public function office_account() {
        return $this->belongsTo('App\OfficeAccount', 'office_account_id');
    }
}
