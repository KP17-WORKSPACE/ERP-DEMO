<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmPostalReceivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_postal_receives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from_title')->nullable();
            $table->string('to_title')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('address')->nullable();
            $table->date('date')->nullable();
            $table->text('note')->nullable();
            $table->string('file')->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('sm_postal_receives');
    }
}
