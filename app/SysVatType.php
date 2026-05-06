<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysVatType extends Model
{
    protected $table = 'sys_vat_type';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'type','status'
    ];
}