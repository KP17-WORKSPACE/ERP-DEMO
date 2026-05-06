<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmBankAccount;

class CreateSmBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_bank_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->double('opening_balance', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->string('created_by',255)->default('User')->comment('User can edit, delete, System can edit');
            $table->tinyInteger('active_status')->default(1);
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });


        // $account = new SmBankAccount();
        // $account->account_name = 'Petty Cash'; 
        // $account->opening_balance = 0; 
        // $account->created_by = 'System'; 
        // $account->save(); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_bank_accounts');
    }
}
