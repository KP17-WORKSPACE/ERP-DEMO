<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffActivity extends Model
{
   

    protected $table = 'staff_activities';

    protected $fillable = [
        'staff_id',
        'doc_number',
        'type',
        'message',
        'user_id',
        'created_at',
        'updated_at',
        
    ];

    public function staff()
    {
        return $this->belongsTo(SmStaff::class, 'staff_id');
    }
}
