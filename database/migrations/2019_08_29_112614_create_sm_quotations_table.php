<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmQuotation;
class CreateSmQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_quotations', function (Blueprint $table) {
            $table->increments('id'); 
            $table->string('quotation_type',255)->nullable();
            $table->string('title',255)->nullable();
            $table->string('number',255)->nullable(); 
            $table->date('date')->nullable();
            $table->string('reference',255)->nullable();


            $table->integer('customer_id')->unsigned();
            $table->string('customer_name',255)->nullable();

            $table->integer('vendor_id')->unsigned();
            $table->string('vendor_name',255)->nullable();

            $table->double('amount', 15, 2);
            $table->double('discount_amount', 15, 2)->nullable();
            $table->enum('discount_type', ['P', 'A'])->nullable()->comment('P = percentage, A= amount'); 
            $table->double('tax_amount', 15, 2)->nullable(); 
 


            $table->enum('payment_status',  ['UP', 'P', 'PP', 'PR'] )->comment('UP= UNPAID , P= PAID , PP= PARTIALLY PAID, PR= PROFORMA');
            $table->double('partial_paymemt', 15, 2)->nullable();


            $table->text('note')->nullable();
            $table->text('description')->nullable();

            $table->text('private_note')->nullable();
            $table->text('public_note')->nullable();
            $table->text('terms_note')->nullable();
            $table->text('footer_note')->nullable();
            $table->string('signature_person',255)->nullable();
            $table->string('signature_company',255)->nullable(); 
            $table->tinyInteger('is_approved')->default(1)->comment('0 = no, 1= yes');
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable()->default(1);
            $table->string('updated_by')->nullable()->default(1);
            $table->timestamps();
        });
        // for($i=1; $i<=10; $i++){
        //     $s = new SmQuotation();
        //     $s->title ='Title '.$i;
        //     $s->number ='45646'.$i;
        //     $s->date =date('Y-m-d');
        //     $s->customer_id =1;
        //     $s->customer_name = 'Rashed Zaman';
        //     $s->vendor_id =1;
        //     $s->vendor_name = 'Google Inc.';
        //     $s->amount =5515.89;
        //     $s->discount_amount =15.89;
        //     $s->discount_type ='P';
        //     $s->tax_amount =5;
        //     $s->payment_status ='PP';
        //     $s->partial_paymemt =45789.67*$i;
        //     $s->private_note ='private_note '.$i;
        //     $s->public_note ='public_note '.$i;
        //     $s->terms_note ='terms_note '.$i;
        //     $s->signature_person ='signature_person '.$i;
        //     $s->signature_company ='signature_company '.$i;
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
        Schema::dropIfExists('sm_quotations');
    }
}
