<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmItemCategory;
class CreateSmItemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_item_categories', function (Blueprint $table) {
            $table->increments('id');
             $table->string('category_name',100)->nullable(); 
            $table->timestamps();
        });


        // $store = new SmItemCategory();
        // $store->category_name = 'Raw Materials Inventory';
        // $store->save();

        // $store = new SmItemCategory();
        // $store->category_name = 'Transit Inventory';
        // $store->save();

        // $store = new SmItemCategory();
        // $store->category_name = 'Buffer Inventory';
        // $store->save();

        // $store = new SmItemCategory();
        // $store->category_name = 'Application Inventory';
        // $store->save();

        // $store = new SmItemCategory();
        // $store->category_name = 'Enterprice Inventory';
        // $store->save();

        // $store = new SmItemCategory();
        // $store->category_name = 'Others Inventory';
        // $store->save();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_item_categories');
    }
}
