<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('note',255)->nullable();
            $table->enum('action', ['Edit', 'Delete','Insert','Inactive','Active']);   
            $table->integer('action_id')->nullable();    
            $table->string('model_name',255)->nullable();    
            $table->integer('author_id')->nullable();    
            $table->string('author_mode',255)->nullable();    
            $table->text('old_data')->nullable();    
            $table->text('new_data')->nullable();    
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
        Schema::dropIfExists('sm_activities');
    }
}
