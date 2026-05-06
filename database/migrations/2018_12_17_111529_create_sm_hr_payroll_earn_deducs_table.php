<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmHrPayrollEarnDeducsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_hr_payroll_earn_deducs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payroll_generate_id')->unsigned();
            $table->string('type_name')->nullable();
            $table->integer('amount')->length(50)->nullable();
            $table->string('earn_dedc_type')->length(5)->nullable()->comment('e for earnings and d for deductions');
            $table->tinyInteger('active_status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_hr_payroll_earn_deducs');
    }
}
