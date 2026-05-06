\<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmAddExpense; 
class CreateSmAddExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_add_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable();
            $table->tinyInteger('expense_head_id')->nullable();
            $table->tinyInteger('expense_sub_head_id')->nullable();
            $table->tinyInteger('account_id')->nullable();
            $table->tinyInteger('payment_method_id')->nullable();
            $table->date('date')->nullable();
            $table->decimal('amount', 16, 2); 
            $table->string('file')->nullable();
            $table->text('description')->nullable();
            $table->integer('cost_center_id')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->tinyInteger('status')->default(0)->comment('0 pending, 1 approved, 2 cancelled');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });


        // $store = new SmAddExpense();
        // $store->name                =           'demo expense data 1';
        // $store->expense_head_id     =           4;
        // $store->expense_sub_head_id     =           40;
        // $store->payment_method_id   =           1;
        // $store->date                =           '2019-05-05';
        // $store->amount              =           1200; 
        // $store->save();



        // $store = new SmAddExpense();
        // $store->name                =           'demo expense data 2';
        // $store->expense_head_id     =           2;
        // $store->expense_sub_head_id     =           20;
        // $store->payment_method_id   =           1;
        // $store->date                =           '2019-05-05';
        // $store->amount              =           15000; 
        // $store->save(); 


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_add_expenses');
    }
}
