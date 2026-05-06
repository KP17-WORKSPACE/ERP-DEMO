<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmAddExpense extends Model
{
    public function expenseHead(){
    	return $this->belongsTo('App\SmExpenseHead', 'expense_head_id', 'id');
    }
    public function expenseSubHead(){
        return $this->belongsTo('App\SmExpenseHead', 'expense_head_id', 'id');
    }

    public function ACHead(){
    	return $this->belongsTo('App\SmChartOfAccount','expense_head_id', 'id');
    }


    public function ACSubHead(){
        return $this->belongsTo('App\SmSubAccount','expense_sub_head_id', 'id');
    }



    public function account(){
    	return $this->belongsTo('App\SmBankAccount', 'account_id', 'id');
    }

    public function paymentMethod(){
    	return $this->belongsTo('App\SmPaymentMethhod', 'payment_method_id', 'id');
    }

    public function costCenter(){
        return $this->belongsTo('App\SmCostCenter', 'cost_center_id', 'id');
    }
}
