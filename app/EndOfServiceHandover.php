<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EndOfServiceHandover extends Model
{
    protected $table = 'sm_end_of_service_handover';
    protected $guarded = [];

    public function endOfService()
    {
        return $this->belongsTo(EndOfService::class, 'end_of_service_id');
    }
}
