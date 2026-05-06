<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmQuestionBankMuOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_question_bank_mu_options', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('question_bank_id')->nullable();
            $table->string('title')->nullable();
            $table->tinyInteger('status')->nullable()->comment('0 = false, 1 = correct');
            $table->tinyInteger('active_status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('school_id')->nullable()->default(1);
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
        Schema::dropIfExists('sm_question_bank_mu_options');
    }
}
