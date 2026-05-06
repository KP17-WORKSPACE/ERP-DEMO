<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class SmDesignation extends Model   
{

    //
public function department()
{
    return $this->belongsTo(SmHumanDepartment::class, 'department_id', 'id');
}
}

