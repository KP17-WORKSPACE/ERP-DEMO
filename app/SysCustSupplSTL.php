<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCustSupplSTL extends Model
{
    protected $table = 'sys_cust_suppl_stl';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id','cust_suppl_id','stl_bank','stl_limit','stl_per_trn_limit','stl_opb','status','created_by','created_at','updated_by','updated_at','company_id','stl_dept'
    ];
    
}
