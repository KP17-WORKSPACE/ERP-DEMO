<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmStaffDocument extends Model
{
    //
        protected $table = 'sm_staff_documents';
        protected $fillable = [
        'staff_id','group','key','name','path','remarks','expiry_date','document_number'
        ];
}
