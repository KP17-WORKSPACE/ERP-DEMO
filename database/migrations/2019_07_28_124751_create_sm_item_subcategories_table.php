<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmItemSubcategory;
class CreateSmItemSubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_item_subcategories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->nullable();
             $table->string('sub_category_name',255)->nullable(); 
            $table->timestamps();
        });


        // for($i=1; $i<=5; $i++){
        //     $s = new SmItemSubcategory();
        //     $s->category_id = $i; 
        //     $s->sub_category_name = 'Sub Category '.$i;
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
        Schema::dropIfExists('sm_item_subcategories');
    }
}
