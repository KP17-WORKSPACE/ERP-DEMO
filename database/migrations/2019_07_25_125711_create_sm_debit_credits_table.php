<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmDebitCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //voucher_no, date, type, note, customer, receiver, company_or_address, amount, authorised_signature, accountatnt_signature, active_status,
        
        Schema::create('sm_debit_credits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('voucher_no',255)->nullable();
            $table->date('date')->nullable();
            $table->enum('type', ['D', 'C'])->comment('d debit, c credit');    
            $table->text('note',255)->nullable();
            $table->text('customer',255)->nullable();
            $table->text('receiver',255)->nullable();
            $table->text('company_or_address',255)->nullable();
            $table->double('amount')->nullable();
            $table->string('authorised_signature',255)->nullable();
            $table->string('accountant_signature',255)->nullable();

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
        Schema::dropIfExists('sm_debit_credits');
    }
}
