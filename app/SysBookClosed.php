<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysBookClosed extends Model
{
    protected $table = 'sys_book_closed';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','book_name','book_closed','company_id','status','updated_by','updated_at'
    ];

    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'id');
    }
}