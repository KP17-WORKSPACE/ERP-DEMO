<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmInventoryPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_inventory_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_receive_sell_id')->nullable()->unsigned();
            $table->date('payment_date')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->string('reference_no', 50)->nullable();
            $table->string('payment_type')->length(11)->nullable()->comment('R for receive S for sell');
            $table->integer('payment_method')->nullable()->unsigned();
            $table->string('notes')->length(500)->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->tinyInteger('created_by')->nullable();
            $table->tinyInteger('updated_by')->nullable();
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
        Schema::dropIfExists('sm_inventory_payments');
    }
}
