<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCrmAmcTableServiceScopeofWork extends Model
{
    protected $table = 'sys_crm_amc_table_service_scope_of_work';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','amc_id','work','updated_at'
    ];
}