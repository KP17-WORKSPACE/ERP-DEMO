<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmStaffAttendence;
class CreateSmStaffAttendencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_staff_attendences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id')->unsigned();
            $table->string('attendence_type',10)->nullable()->comment('Present: P Late: L Absent: A Holiday: H Half Day: F');
            $table->text('notes')->nullable();
            $table->date('attendence_date')->nullable();

            $table->string('in_time',20)->nullable();
            $table->string('out_time',20)->nullable();

            $table->tinyInteger('created_by')->nullable();
            $table->tinyInteger('updated_by')->nullable(); 
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
        Schema::dropIfExists('sm_staff_attendences');
    }
}
