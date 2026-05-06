<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmCostCenter;
use Faker\Factory as Faker;
class CreateSmCostCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_cost_centers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255)->nullable();
            $table->string('description',255)->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->tinyInteger('is_existing_item')->default(0);
            $table->integer('item_id')->nullable();
            $table->timestamps();
        });


        // $faker = Faker::create();

        // for ($i=1; $i<=3; $i++) {
        //    $s = new SmCostCenter();
        //    $s->name = $i;
        //    $s->description = 'cost center '.$i;
        //    $s->is_existing_item = 0;
        //    $s->item_id = NULL;
        //    $s->save();
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_cost_centers');
    }
}
