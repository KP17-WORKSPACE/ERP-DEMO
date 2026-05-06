<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmChartOfAccount;
class CreateSmChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_chart_of_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('head', 50)->nullable();
            $table->string('type', 1)->nullable()->comment('E = expense, I = income');
            $table->integer('is_daily_expense_head')->default(0);
            $table->integer('active_status')->nullable()->default(1);
            $table->string('created_by')->nullable()->default(1); 
            $table->string('updated_by')->nullable()->default(1);
            $table->timestamps();
        });

 

        //ID=1
        // $store = new SmChartOfAccount();
        // $store->head = 'Wages from labor';
        // $store->type = 'I';
        // $store->save();

        // $store = new SmChartOfAccount();
        // $store->head = 'Capital from labor';
        // $store->type = 'I';
        // $store->save();

        // $store = new SmChartOfAccount();
        // $store->head = 'Rental income';
        // $store->type = 'I';
        // $store->save();

        // $store = new SmChartOfAccount();
        // $store->head = 'Windfall income';
        // $store->type = 'I'; 
        // $store->save();

        // $store = new SmChartOfAccount();
        // $store->head = 'Capital gains';
        // $store->type = 'I';
        // $store->save();

        // $store = new SmChartOfAccount();
        // $store->head = 'Partnership income';
        // $store->type = 'I';
        // $store->save();

        // $store = new SmChartOfAccount();
        // $store->head = 'Interest';
        // $store->type = 'I';
        // $store->save();
        //Id=7



        $expense= ['Telephone Expenses','Travelling Expenses','Office Equipment and Supplies','Utility Expenses','Property Tax','Legal Expenses','Bank Charges','Repair and Maintenance Expenses','Insurance Expenses ','Advertising Expenses','Entertainment Expenses','Sales Expenses','Freight in Cost','Freight out Cost','Product Cost','Rental Cost','Depreciation Expenses'];

        // foreach ($expense as $value) {
        //     $store = new SmChartOfAccount();
        //     $store->head = $value;
        //     $store->type = 'E';
        //     $store->save();
        // }




    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_chart_of_accounts');
    }
}
