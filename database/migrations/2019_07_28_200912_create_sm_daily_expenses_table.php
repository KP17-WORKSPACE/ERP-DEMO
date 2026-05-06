<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmDailyExpense;
class CreateSmDailyExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_daily_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('head_id')->nullable();
            $table->integer('sub_head_id')->nullable();
            $table->decimal('amount', 16, 2)->nullable();
            $table->integer('cost_center_id')->nullable();
            $table->text('description')->nullable();
            $table->integer('is_approved')->default(0);
            $table->date('date')->nullable();

            $table->integer('created_by')->nullable(); 
            $table->integer('updated_by')->nullable(); 
            $table->integer('active_status')->default(1); 
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
        Schema::dropIfExists('sm_daily_expenses');
    }
}
