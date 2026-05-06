<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmItemSellChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_item_sell_children', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_sell_id')->nullable();
            $table->integer('item_id')->nullable();
            $table->integer('sell_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('sub_total')->nullable();
            $table->string('description')->length('500')->nullable();
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
        Schema::dropIfExists('sm_item_sell_children');
    }
}
