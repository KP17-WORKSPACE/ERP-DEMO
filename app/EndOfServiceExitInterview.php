<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EndOfServiceExitInterview extends Model
{
    protected $table = 'sm_end_of_service_exit_interview';
    protected $guarded = [];

    public function endOfService()
    {
        return $this->belongsTo(EndOfService::class, 'end_of_service_id');
    }
}
