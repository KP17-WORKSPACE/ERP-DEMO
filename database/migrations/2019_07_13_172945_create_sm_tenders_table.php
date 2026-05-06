<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmTender;
use App\SmCompititor;
use Faker\Factory as Faker;
class CreateSmTendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_tenders', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('work_order_mode', ['equipment', 'spareparts'])->nullable();  
            $table->string('tender_title')->nullable();
            $table->string('tender_no')->nullable();
            $table->string('work_order_no')->nullable();
            $table->string('letter_no')->nullable();


            $table->date('work_order_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('open_date')->nullable();


            $table->tinyInteger('customer_id')->nullable();
            $table->tinyInteger('vendor_id')->nullable();
            $table->tinyInteger('department_id')->nullable();


            $table->float('bid_amount', 10, 2)->nullable();
            $table->float('discount_amount', 10, 2)->nullable();
            $table->enum('discount_type', ['P', 'A'])->nullable()->comment('P = percentage, A= amount');
            $table->text('description')->nullable();
            $table->text('note')->nullable();
            $table->text('end_user_name')->nullable();



            //Shipment
            $table->date('shipment_work_order_date')->nullable();
            $table->enum('shipping_mode', ['AIR', 'SEA','LAND'])->nullable();    
            $table->string('shipping_tracking_number', 255)->nullable();
            $table->string('shipping_carrier', 255)->nullable();

 
            //Deliver date
            $table->date('status_delivery_date')->nullable();
            $table->string('status_cr', 200)->nullable();    
            $table->string('status_destination', 255)->nullable();

            //- Inspection Complete
            $table->date('inspection_completion_date')->nullable();

            //Completed
            $table->date('completion_date')->nullable();
            $table->string('cheque_no', 255)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->double('amount',15,2)->nullable();
            $table->string('file1', 255)->nullable();
            $table->string('file2', 255)->nullable();
            $table->string('file3', 255)->nullable();


            $table->tinyInteger('is_approved')->default(0)->comment('0 = no, 1= yes');
            $table->tinyInteger('active_status')->default(1);
            $table->tinyInteger('stage_status')->default(0)->comment('0 = running, 1= shipment, 2= delivered, 3= inspection, 4=completed');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
 

    //     $faker = Faker::create(); 
    //     $sql ="INSERT INTO sm_tenders (id, work_order_mode, tender_title, tender_no, work_order_no, letter_no, work_order_date, delivery_date, open_date, customer_id, vendor_id, department_id, bid_amount, discount_amount, discount_type, description, note, end_user_name, shipment_work_order_date, shipping_mode, shipping_tracking_number, status_delivery_date, status_cr, status_destination, inspection_completion_date, completion_date, cheque_no, bank_name, amount, file1, file2, file3, is_approved, active_status, created_by, updated_by, created_at, updated_at) VALUES (1, 'equipment', '40 HP OUT BOARD ENGINE (MERCURY,EVINRUDE,YAMAHA)', '23.02.2608.212.53.364.19-20.0102', '345', '345', '2019-08-27', '2019-08-27', '2019-08-27', 2, NULL, 1, 35840.00, 0.00, 'A', NULL, 'The Tender Title has an alignment problem. Inspecting Department, End User Name is missing from edit mode of a work order. Edit mode is still showing the Signature field.', 'Here the vendor', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, '1', NULL, '2019-08-27 05:40:59', '2019-08-27 05:40:59')";
    // DB::statement($sql);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_tenders');
    }
}
