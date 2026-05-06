<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysReceiptMode extends Model
{
    protected $table = 'sys_receipt_mode';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'title','active_status','created_by','updated_by','created_at','updated_at'
    ];    
}
