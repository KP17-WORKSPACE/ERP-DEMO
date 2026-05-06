<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmBusinessEntityType extends Model
{
    //

 protected $table = 'sys_business_entity_types';

    protected $fillable = [
    'name',
    'active_status'
    ];

}
