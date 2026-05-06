<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmHrSalaryTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_hr_salary_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('salary_grades',50)->nullable();
            $table->string('salary_basic',50)->nullable();
            $table->string('overtime_rate',50)->nullable();
            $table->integer('house_rent')->length(50)->nullable();
            $table->integer('provident_fund')->length(50)->nullable();
            $table->integer('gross_salary')->length(50)->nullable();
            $table->integer('total_deduction')->length(50)->nullable();
            $table->integer('net_salary')->length(50)->nullable();
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
        Schema::dropIfExists('sm_hr_salary_templates');
    }
}
