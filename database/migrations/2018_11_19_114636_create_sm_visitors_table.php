<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_visitors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('phone', 20)->nullable();
            $table->string('visitor_id', 30)->nullable();
            $table->tinyInteger('no_of_person')->nullable();
            $table->string('purpose')->nullable();
            $table->date('date')->nullable();
            $table->string('in_time', 30)->nullable();
            $table->string('out_time', 30)->nullable();
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
        Schema::dropIfExists('sm_visitors');
    }
}
