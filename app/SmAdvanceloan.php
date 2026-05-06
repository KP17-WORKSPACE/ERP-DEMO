<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class SmAdvanceloan extends Model

{

  protected $fillable = [
    'staff_id','type_id','amount','installments','amount_per_month',
    'repayment_start','repayment_mode','purpose','attachment',
    'status','manager_approval','finance_approval','hr_approval',
    'manager_approved_by','finance_approved_by','hr_approved_by',
    'approved_at','note','date'
];


    public static function totalDeduction($id){

    	$payroll_generates = SmHrPayrollGenerate::where('staff_id', $id)->get();

    	$total_deduction = 0; 

    	foreach($payroll_generates as $payroll_generate){

    		$total_deduction = $total_deduction + $payroll_generate->total_deduction;

    	}

    	return $total_deduction;	

    }









    public static function staffDetail($id){

        $staffDetails = SmStaff::find($id);

        

        return $staffDetails;    

    }









    public function staffDetails(){

    	return $this->belongsTo('App\SmStaff', 'staff_id', 'id');

    }

}

