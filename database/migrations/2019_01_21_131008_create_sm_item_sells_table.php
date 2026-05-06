<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmItemSellsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_item_sells', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->nullable();
            $table->integer('student_staff_id')->nullable();
            $table->date('sell_date')->nullable();
            $table->string('reference_no', 50)->nullable();
            $table->integer('grand_total')->nullable();
            $table->integer('total_quantity')->nullable();
            $table->integer('total_paid')->nullable();
            $table->integer('total_due')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('paid_status')->nullable()->coment('P = paid, PP = partially paid, U = unpaid, R = ----');
            $table->string('description')->length(500)->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('sm_item_sells');
    }
}
