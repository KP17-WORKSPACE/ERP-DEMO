<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmCashIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_cash_issues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('return_date')->nullable();
            $table->double('amount', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->enum('is_return', [0,1])->default(0)->comment('0 means not return, 1 means returned');
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
        Schema::dropIfExists('sm_cash_issues');
    }
}
