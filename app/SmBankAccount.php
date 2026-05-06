<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmBankAccount extends Model
{
   public static function bankBalance($id){

   		$opening_balance = SmBankAccount::find($id);

        $incomes = SmAddIncome::where('payment_method_id', 3)->where('account_id', $id)->sum('amount');

        $expenses = SmAddExpense::where('payment_method_id', 3)->where('account_id', $id)->where('status', 1)->sum('amount');

        $transfer_amount = SmFundTransfer::where('bank_account_id', $id)->sum('amount');


        $payroll_amount = SmHrPayrollGenerate::where('payment_mode', 3)->where('account_id', $id)->sum('net_salary');
        
        $total_balance = $opening_balance->opening_balance + $incomes + $transfer_amount - $expenses - $payroll_amount;

        return $total_balance;
   }

   public static function bankDetails($date_from, $date_to, $id){

	   	$date_from = date('Y-m-d', strtotime($date_from));
	   	$date_to = date('Y-m-d', strtotime($date_to));

   		$opening_balance = SmBankAccount::find($id);

        $incomes = SmAddIncome::where('payment_method_id', 3)->where('account_id', $id)->where('date', '>=', $date_from)->where('date', '<=', $date_to)->sum('amount');

 

        $expenses = SmAddExpense::where('payment_method_id', 3)->where('account_id', $id)->where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('status', 1)->sum('amount');
        
        $total_balance = $opening_balance->opening_balance + $incomes - $expenses;

        $data = [];
        $data['opening_balance']  = $opening_balance->opening_balance;
        $data['income']  = $incomes;
        $data['expense']  = $expenses;


        return $data;
   }
}
