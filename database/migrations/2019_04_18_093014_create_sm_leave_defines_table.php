<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmLeaveDefine;

class CreateSmLeaveDefinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_leave_defines', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('role_id')->nullable();
            $table->tinyInteger('type_id')->nullable();
            $table->integer('days')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable()->default(1);
            $table->string('updated_by')->nullable()->default(1);
            $table->timestamps();
        });

        // for ($role_id = 1; $role_id <= 3; $role_id+=2) {
        //     for ($leave_type = 1; $leave_type <= 7; $leave_type++) {
        //         $s = new SmLeaveDefine();
        //         $s->role_id = $role_id;
        //         $s->type_id = $leave_type;
        //         $s->days = 10;
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
        Schema::dropIfExists('sm_leave_defines');
    }
}
