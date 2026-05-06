<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmSubAccount;
class CreateSmSubAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_sub_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('head_id');
            $table->string('sub_head', 250)->nullable(); 
            $table->string('description', 250)->nullable(); 
            $table->integer('active_status')->default(1);
            $table->integer('is_approved')->default(0);
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
        Schema::dropIfExists('sm_sub_accounts');
    }
}
