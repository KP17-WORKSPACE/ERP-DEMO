<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmCompititor;
use Faker\Factory as Faker;
class CreateSmCompititorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_compititors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tender_id')->nullable();
            $table->tinyInteger('lowest_bid')->default(0)->comment('0 no, 1 yes');
            $table->integer('company_id')->nullable();
            $table->string('company_name',255)->nullable();
            $table->string('remark',255)->nullable();
            $table->decimal('company_bid_amount', 20,2)->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
        });



        // $faker = Faker::create();

        // for($i=1; $i<=10; $i++){
        //     for($j=1; $j<=50; $j++){
        //         $s= new SmCompititor();
        //         $s->tender_id = $i;
        //         $s->company_name = $faker->sentence($nbWords = 2, $variableNbWords = true).' Ltd.';
        //         $s->remark = $faker->sentence($nbWords = 3, $variableNbWords = true);
        //         $s->company_bid_amount = 1265.00 + rand()%10+ $faker->randomDigit;
        //         $s->date = date('Y-m-d');
        //         $s->save();

        //     }
        // }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_compititors');
    }
}
