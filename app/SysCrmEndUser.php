<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class SysCrmEndUser extends Model

{

    protected $table = 'sys_crm_end_user';

    protected $primaryKey = 'id';



    protected $fillable = [

        'id','deal_id','end_user_company_name','address_line_a','address_line_b','city','po_box','end_user_contact_person','job_title','mobile_no','email','project_name','project_description','expected_close_date','status','created_by','updated_by','created_at','updated_at','device_serial'

    ];

    

    public function createdby(){

        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');

    }

    public function dealid(){

        return $this->belongsTo('App\SysCrmDeals', 'deal_id', 'id');

    }

}