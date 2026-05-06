<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfixInvoiceCategoryLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infix_invoice_category_links', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->timestamps();
        });

        DB::table('infix_invoice_category_links')->insert([
            [
                'name' => 'Payment Method',    //  
            ],
            [
                'name' => 'Discount Amount',    //  
            ],
            [
                'name' => 'Discount Type',    //   
            ],
            [
                'name' => 'TAX/GST/VAT',    //     
            ],
            [
                'name' => 'Customer',    //    
            ],
            [
                'name' => 'Project',    //    
            ],
            [
                'name' => 'Client',    //    
            ],
            [
                'name' => 'Currency',    //    
            ],
            [
                'name' => 'Recurring Invoice',    //    
            ],
            [
                'name' => 'Invoice Number',    //    
            ],
            [
                'name' => 'Invoice Date',    //    
            ],
            [
                'name' => 'Due Date',    //    
            ]

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infix_invoice_category_links');
    }
}
