<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spondonit\Invoice\Models\InfixInvoice;
use Spondonit\Invoice\Models\InfixInvoiceProduct;


class CreateInfixInvoiceProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infix_invoice_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->integer('product_id')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->float('price', 10,2)->nullable();
            $table->foreign('invoice_id')->references('id')->on('infix_invoices')->onDelete('cascade');
            $table->timestamps();
        });

        $customers = App\SmStaff::where('role_id',2)->get();
        foreach ($customers as $customer) {
            $s= new InfixInvoice();
            $s->customer_id =$customer->id;
            $s->invoice_number =time();
            $s->invoice_due_date =date('Y-m-d');
            $s->currency_id =2;
            $s->project_id =1;
            $s->payment_method_id =1;
            $s->is_recurring_invoice =1;
            $s->recurring_cycle ='M';
            $s->payment_status ='P';
            $s->save();
            $insertedId = $s->id;


            $invoice_product = new InfixInvoiceProduct();
            $invoice_product->invoice_id = $insertedId;
            $invoice_product->product_id =1;
            $invoice_product->description = 'demo text';
            $invoice_product->quantity = 5;
            $invoice_product->price = 5000;
            $invoice_product->save();


        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infix_invoice_products');
    }
}
