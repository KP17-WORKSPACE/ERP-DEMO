<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmItemStore;
class CreateSmItemStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_item_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_name',100)->nullable();
            $table->string('store_no',100)->nullable();
            $table->string('description',500)->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->timestamps();
        });

        // for($i=1; $i<=5; $i++){
        //     $s = new SmItemStore();
        //     $s->store_name = 'Store '.$i;
        //     $s->store_no = 100*$i;
        //     $s->description = 'Store '.$i;
        //     $s->save();
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_item_stores');
    }
}
