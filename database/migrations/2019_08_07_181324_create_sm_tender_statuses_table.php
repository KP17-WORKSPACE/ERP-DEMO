<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmTenderStatus;
class CreateSmTenderStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_tender_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255)->nullable();
            $table->tinyInteger('is_approved')->default(0)->comment('0 = no, 1= yes');
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable()->default(1);
            $table->string('updated_by')->nullable()->default(1);
            $table->timestamps();
        });
        
        // $status_array = ['Running','Shipment','Delivered','Inspection Complete','Completed'];
        // foreach ($status_array as $row) {
        //     $s = new SmTenderStatus();
        //     $s->name = $row;
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
        Schema::dropIfExists('sm_tender_statuses');
    }
}
