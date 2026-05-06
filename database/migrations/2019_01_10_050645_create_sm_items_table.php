<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmItem;
class CreateSmItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_name',100)->nullable();
            $table->integer('category_name')->nullable();
            $table->integer('subcategory_name')->nullable();
            $table->float('total_in_stock')->nullable()->default(1);
            $table->string('description',500)->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->timestamps();
        });

        // for($i=1; $i<=5; $i++){
        //     $s = new SmItem();
        //     $s->item_name = 'Item name '.$i;
        //     $s->category_name =$i;
        //     $s->subcategory_name =$i;
        //     $s->total_in_stock = 23*$i;
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
        Schema::dropIfExists('sm_items');
    }
}
