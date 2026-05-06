<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysBookClosedDocNumber extends Model
{
    protected $table = 'sys_book_closed_doc_number';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','book_id ','closing_date','doc_number','company_id','status','created_by','created_at','updated_by','updated_at'
    ];

    public function book(){
        return $this->belongsTo('App\SysBookClosed', 'book_id', 'id');
    }

    public function createdby(){
        return $this->belongsTo('App\SmStaff', 'created_by', 'id');
    }

    public function updatedby(){
        return $this->belongsTo('App\SmStaff', 'updated_by', 'id');
    }
}