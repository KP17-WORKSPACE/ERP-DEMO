<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysCustSupplDocForm extends Model
{
    protected $table = 'sys_cust_suppl_doc_form';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','cust_suppl_id','doc_name','doc_file','doc_exp_date','status','created_by','created_at','updated_by','updated_at'
    ];
}