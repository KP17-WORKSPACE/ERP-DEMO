<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmItemSubcategoryCartTable extends Migration
{
    public function up()
    {
        Schema::create('sm_item_subcategory_cart', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('category_name');
            $table->string('sub_category_name');
            $table->integer('company_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sm_item_subcategory_cart');
    }
}
