<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmPaymentMethhodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_payment_methhods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('method', 255);
            $table->string('type')              ->nullable(); 
            $table->tinyInteger('active_status')->default(1);
            $table->tinyInteger('gateway_id')   ->default(1);
            $table->string('created_by')        ->nullable()->default(1);
            $table->string('updated_by')        ->nullable()->default(1);
            $table->timestamps();
        });
        DB::table('sm_payment_methhods')->insert([
            [
                'method' => 'Cash',
                'type' => 'System',
                'gateway_id' => 0
            ],
            [
                'method' => 'Cheque',
                'type' => 'System',
                'gateway_id' => 0
            ],
            [
                'method' => 'Bank',
                'type' => 'System',
                'gateway_id' => 0
            ],
            [
                'method' => 'Paypal',
                'type' => 'System',
                'gateway_id' => 1
            ],
            [
                'method' => 'Stripe',
                'type' => 'System',
                'gateway_id' => 2
            ],
            [
                'method' => 'Paystack',
                'type' => 'System',
                'gateway_id' => 3
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
        Schema::dropIfExists('sm_payment_methhods');
    }
}
