<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmBrandManagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_brand_manages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255)->nullable();
            $table->bigInteger('parent_id')->default(0);
            $table->string('description',255)->nullable();
            $table->tinyInteger('active_status')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('sm_brand_manages');
    }
}
