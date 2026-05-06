<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmLeaveRequest;
use App\SmStaff;
class CreateSmLeaveRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_leave_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('leave_define_id')->nullable();
            $table->integer('staff_id')->nullable()->unsigned();
            $table->integer('role_id')->nullable()->unsigned();
            $table->date('apply_date')->nullable();
            $table->tinyInteger('type_id')->nullable();
            $table->date('leave_from')->nullable();
            $table->date('leave_to')->nullable();
            $table->text('reason')->nullable();
            $table->text('note')->nullable();
            $table->string('file')->nullable();
            $table->string('approve_status')->nullable()->comment('P for Pending, A for Approve, R for reject'); 
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable()->default(1);
            $table->string('updated_by')->nullable()->default(1);
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
        Schema::dropIfExists('sm_leave_requests');
    }
}
