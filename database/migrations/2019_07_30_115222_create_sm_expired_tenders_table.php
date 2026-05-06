<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmExpiredTender;
class CreateSmExpiredTendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_expired_tenders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('upcoming_tender_id')->nullable(); 
            $table->string('tender_result',255)->nullable(); 
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
        Schema::dropIfExists('sm_expired_tenders');
    }
}
