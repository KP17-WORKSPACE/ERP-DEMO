<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmSmsGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_sms_gateways', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gateway_name')->nullable();
            $table->string('clickatell_username')->nullable();
            $table->string('clickatell_password')->nullable();
            $table->string('clickatell_api_id')->nullable();
            $table->string('twilio_account_sid')->nullable();
            $table->string('twilio_authentication_token')->nullable();
            $table->string('twilio_registered_no')->nullable();
            $table->string('msg91_authentication_key_sid')->nullable();
            $table->integer('msg91_sender_id')->nullable();
            $table->integer('msg91_route')->nullable();
            $table->integer('msg91_country_code')->nullable();
            $table->tinyInteger('active_status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('school_id')->nullable()->default(1);
            $table->timestamps();
        });
        DB::table('sm_sms_gateways')->insert([
            [
                'gateway_name' => 'Clickatell',
                'clickatell_username'=>'demo1',
                'clickatell_password'=>'122334'
            ],
            [
                'gateway_name' => 'Twilio',
                'clickatell_username'=>'demo2',
                'clickatell_password'=>'12336'
            ],
            [
                'gateway_name' => 'Msg91',
                'clickatell_username'=>'demo3',
                'clickatell_password'=>'23445'
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
        Schema::dropIfExists('sm_sms_gateways');
    }
}
