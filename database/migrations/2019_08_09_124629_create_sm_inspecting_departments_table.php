<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Faker\Factory as Faker;
use App\SmInspectingDepartment;
class CreateSmInspectingDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_inspecting_departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('department_name',255)->nullable();
            $table->string('name',255)->nullable();
            $table->string('phone',255)->nullable();
            $table->string('email',255)->nullable();
            $table->string('description',255)->nullable();  
            $table->tinyInteger('is_approved')->default(1)->comment('0 = no, 1= yes');
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable()->default(1);
            $table->string('updated_by')->nullable()->default(1);
            

            $table->timestamps();
        });



        // $faker = Faker::create();
        // for($i=1; $i<=5; $i++){
        //     $s = new SmInspectingDepartment();
        //     $s->department_name = $faker->sentence(4);
        //     $s->name = $faker->name; 
        //     $s->phone = $faker->randomNumber($nbDigits = 8, $strict = false);  
        //     $s->email =$faker->email; 
        //     $s->description = $faker->sentence($nbWords =3, $variableNbWords = true); 
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
        Schema::dropIfExists('sm_inspecting_departments');
    }
}
