<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmAddIncome;
class CreateSmAddIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_add_incomes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable();
            $table->tinyInteger('income_head_id')->nullable();
            $table->tinyInteger('account_id')->nullable();
            $table->tinyInteger('payment_method_id')->nullable();
            $table->tinyInteger('income_sub_head_id')->nullable();
            $table->date('date')->nullable();
            $table->decimal('amount', 16, 2);
            $table->string('file')->nullable();
            $table->text('description')->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('sm_add_incomes');
    }
}
