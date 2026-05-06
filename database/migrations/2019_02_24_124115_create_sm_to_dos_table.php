<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Faker\Factory as Faker;
use App\SmToDo;
class CreateSmToDosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_to_dos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('todo_title')->nullable();
            $table->date('date')->nullable();
            $table->string('complete_status')->default('P')->comment('C for complete, N for not Complete, P Pending')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->integer('created_by')->nullable()->default(1);
            $table->integer('updated_by')->nullable()->default(1);
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
        Schema::dropIfExists('sm_to_dos');
    }
}
