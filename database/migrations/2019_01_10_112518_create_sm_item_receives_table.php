<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmItemReceive;
class CreateSmItemReceivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_item_receives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('supplier_id')->nullable();
            $table->integer('store_id')->nullable();
            $table->date('received_date')->nullable();
            $table->text('description')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('receive_date')->nullable();
            $table->string('grand_total')->nullable();
            $table->string('total_quantity')->nullable(); 

     
            $table->integer('total_due')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('paid_status')->nullable();



            $table->string('part_number')->nullable();
            $table->string('new_part_number')->nullable();
            $table->string('denomination')->nullable();
            $table->integer('qnt')->nullable();
            $table->float('unit_price', 10, 2)->nullable();
            $table->float('total_paid', 10, 2)->nullable();


            $table->float('sale_price', 10, 2)->nullable();
        
            $table->tinyInteger('active_status')->default(1);
            $table->string('created_by')->nullable()->default(1);
            $table->string('updated_by')->nullable()->default(1);
            $table->timestamps();
        });

        // for($i=1; $i<=5; $i++){
        //     $s = new SmItemReceive();
        //     $s->supplier_id = 1;
        //     $s->store_id = 1;
        //     $s->received_date = date('Y-m-d');
        //     $s->description ='description' ;
        //     $s->product_id = $i;
        //     $s->reference_no = $i;
        //     $s->receive_date = date('Y-m-d') ;
        //     $s->part_number =$i ;
        //     $s->unit_price =$i*1276 ;
        //     $s->sale_price =$i*1280 ;
        //     $s->qnt =$i+100 ;
        //     $s->denomination ='(kg)' ;
        //     $s->total_paid =($i+100)*$i*1276  ;
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
        Schema::dropIfExists('sm_item_receives');
    }
}
