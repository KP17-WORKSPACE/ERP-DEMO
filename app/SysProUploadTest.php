<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SmTender;

class SysProUploadTest extends Model
{
    protected $table = 'sys_pro_upload_test';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'name','value'
    ];

}