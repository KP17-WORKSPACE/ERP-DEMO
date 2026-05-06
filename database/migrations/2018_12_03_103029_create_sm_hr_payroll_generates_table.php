<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmHrPayrollGeneratesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_hr_payroll_generates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id')->unsigned();
            $table->integer('basic_salary')->length(50)->nullable();
            $table->integer('total_earning')->length(50)->nullable();
            $table->integer('total_deduction')->length(50)->nullable();
            $table->integer('gross_salary')->length(50)->nullable();
            $table->integer('tax')->length(50)->nullable();
            $table->integer('net_salary')->length(50)->nullable();
            $table->string('payroll_month')->length(20)->nullable();
            $table->string('payroll_year')->length(20)->nullable();
            $table->string('payroll_status')->length(5)->nullable()->comment('NG for not generated, G for generated, P for paid');
            $table->string('payment_mode')->length(15)->nullable();
            $table->date('payment_date')->nullable();

            $table->integer('account_id')->nullable();
            $table->integer('expense_head_id')->nullable();


            $table->string('bank_name')->nullable();
            $table->date('cheque_deposite_date')->nullable();

            $table->string('cheque_no')->nullable();

            $table->string('account_name')->nullable();
            $table->string('account_no')->nullable();


            $table->string('note')->length(500)->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->timestamps();
        });

       //  Schema::table('sm_hr_payroll_generates', function($table) {
       //     $table->foreign('staff_id')->references('id')->on('sm_staffs');
           
       // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_hr_payroll_generates');
    }
}
