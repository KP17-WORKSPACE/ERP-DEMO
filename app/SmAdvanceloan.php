<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class SmAdvanceloan extends Model

{

  protected $fillable = [
    'staff_id','company_id','loan_number','type_id','amount','installments','amount_per_month',
    'repayment_start','repayment_mode','purpose','attachment',
    'status','manager_approval','finance_approval','hr_approval',
    'management_approval','payment_approval',
    'approved_by','manager_approved_by','finance_approved_by','hr_approved_by',
    'management_approved_by','payment_approved_by',
    'manager_remarks','finance_remarks','hr_remarks','management_remarks','payment_remarks',
    'finance_approved_amount','finance_management_approval_req',
    'hr_management_approval_req',
    'payment_voucher_no','payment_date','payment_method','bank_account_id',
    'paid_amount','payment_status','payment_reference',
    'approved_at','finance_approved_at','hr_approved_at','management_approved_at','payment_approved_at',
    'request_type','loan_category','repayment_end_month','requested_disbursement_date',
    'urgency_level','guarantor_employee_id','guarantor_employee_no','guarantor_department',
    'guarantor_contact_number','early_settlement_allowed','grace_period_required',
    'grace_period_months','attachment_remarks','declaration_info_confirmed',
    'declaration_salary_deduction_authorized','declaration_policy_agreed',
    'declaration_final_settlement_agreed','declaration_false_info_understood',
    'declaration_accepted_at','declaration_accepted_by',
    'note','date'
];

    public static function documentNumber($id)
    {
        return 'LN' . str_pad((int) $id, 6, '0', STR_PAD_LEFT);
    }

    public function getDocumentNumberAttribute()
    {
        if (!empty($this->loan_number)) {
            return $this->loan_number;
        }
        return self::documentNumber($this->id);
    }

    public static function totalDeduction($id){
        return SmHrPayrollGenerate::where('staff_id', $id)->sum('total_deduction');
    }

    public static function staffDetail($id){
        $staffDetails = SmStaff::find($id);
        return $staffDetails;
    }

    public function staffDetails(){

    	return $this->belongsTo('App\SmStaff', 'staff_id', 'id');

    }

    public function guarantorStaff()
    {
        return $this->belongsTo('App\SmStaff', 'guarantor_employee_id', 'id');
    }

}
