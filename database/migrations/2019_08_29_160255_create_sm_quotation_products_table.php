<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmQuotationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_quotation_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quotation_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->string('product_model',255)->nullable();
            $table->integer('qnt')->nullable();
            $table->float('unit_price', 10 ,2)->nullable();
            $table->timestamps();
        });


        Schema::table('sm_quotation_products', function($table) {
            $table->foreign('quotation_id')->references('id')->on('sm_quotations'); 
        });



        // $sql ="INSERT INTO sm_quotation_products (id, quotation_id, product_id, product_model, qnt, unit_price, created_at, updated_at) VALUES (1, 1, 1, '23', 3, 1280.00, '2019-08-27 05:40:59', '2019-08-27 05:40:59'),(2, 1, 2, '4', 5, 2560.00, '2019-08-27 05:40:59', '2019-08-27 05:40:59'),(3, 1, 3, '5', 5, 3840.00, '2019-08-27 05:40:59', '2019-08-27 05:40:59')";
        // DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_quotation_products');
    }
}
