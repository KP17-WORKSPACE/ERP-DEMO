<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysBrandCart extends Model
{
    protected $table = 'sys_brand_cart';
    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'company_id',
        'created_by',
        'status',
        'created_at',
        'updated_at'
    ];
}
