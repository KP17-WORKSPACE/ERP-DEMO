<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmUpcomingTender;
use Faker\Factory as Faker;

class CreateSmUpcomingTendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
 
        Schema::create('sm_upcoming_tenders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer')->nullable();
            $table->string('title',255)->nullable();
            $table->string('tender_number')->nullable();
            $table->string('tender_result',255)->nullable();
            $table->date('open_date')->nullable();  
            $table->integer('is_winner')->default(0)->comment('0 no, 1 yes');
            $table->integer('is_expired')->default(0)->comment('0 no, 1 yes');
            $table->integer('winner_compititor_id')->nullable();
            $table->string('notice',255)->nullable();
            $table->string('specifications',255)->nullable();
            $table->integer('work_order_status')->defaulf(0)->nullable();
            $table->timestamps();
        }); 

        // $faker = Faker::create();
        // for($i=1; $i<=10; $i++){
        //     $date = date('Y-m-'). (8+$i).'';
        //     $s = new SmUpcomingTender();
        //     $s->customer = $i;
        //     $s->title = $faker->sentence($nbWords = 6, $variableNbWords = true);
        //     $s->tender_number =$faker->randomNumber($nbDigits =8, $strict = false);
        //     $s->open_date = $date;
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
        Schema::dropIfExists('sm_upcoming_tenders');
    }
}
