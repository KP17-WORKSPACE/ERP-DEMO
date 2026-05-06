<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfixInvoiceSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infix_invoice_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->float('tax', 8,2)->nullable();
            $table->string('tax_type', 5)->default('AD')->comment('AD = After Discount, BD = Before Discount');
            $table->string('prefix')->nullable();
            $table->timestamps();
        });
        DB::table('infix_invoice_settings')->insert([
            [
                'tax' => 0,    //  
                'prefix' => 'infix',    //  
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
        Schema::dropIfExists('infix_invoice_settings');
    }
}
