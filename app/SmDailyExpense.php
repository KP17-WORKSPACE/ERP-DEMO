<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmDailyExpense extends Model
{
    public function expenseHead(){
    	return $this->belongsTo('App\SmChartOfAccount', 'head_id');
    }

    public function SubHead(){
    	return $this->belongsTo('App\SmSubAccount', 'sub_head_id');
    }
    public function costCenter(){
        return $this->belongsTo('App\SmCostCenter', 'cost_center_id', 'id');
    }
}
