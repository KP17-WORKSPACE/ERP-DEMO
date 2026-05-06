<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SmPaymentGatewaySetting;
class CreateSmPaymentGatewaySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_payment_gateway_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gateway_name')      ->nullable();
            $table->string('gateway_username')  ->nullable();
            $table->string('gateway_password')  ->nullable();
            $table->string('gateway_signature') ->nullable();
            $table->string('gateway_client_id') ->nullable();
            $table->string('gateway_mode')      ->nullable(); 
            $table->string('gateway_secret_key')->nullable();
            $table->string('gateway_secret_word')->nullable();
            $table->string('gateway_publisher_key')->nullable();
            $table->string('gateway_private_key')->nullable();
            $table->tinyInteger('active_status')->default(0);
            $table->integer('created_by')       ->nullable();
            $table->integer('updated_by')       ->nullable();
            $table->timestamps();
        });

        $store = new SmPaymentGatewaySetting();
        $store->gateway_name        = 'PayPal';
        $store->gateway_username    = 'demo@paypal.com';
        $store->gateway_client_id   = 'AVZdghanegaOjiL6DPXd0XwjMGEQ2aXc58z1-qNwv_Wz9mI_6MKSW5dS9uPAha3rd7eB82ToOCQLp31c';
        $store->gateway_secret_key  = 'EMgxBzeJ9By7D0xvkSUblDd_GW99WvK0DDNyvkGn7rBikvjPw46xz9Plozp4jl7AOsx';
        $store->save();

        $store = new SmPaymentGatewaySetting();
        $store->gateway_name        = 'Stripe'; 
        $store->gateway_secret_key   = 'AVZdghanegaOjiL6DPXd0XwjMGEQ2aXc58z1-qNwv_Wz9mI_6MKSW5dS9uPAha3rd7eB82ToOCQLp31c';
        $store->gateway_secret_word  = 'EMgxBzeJ9By7D0xvkSUblDd_GW99WvK0DDNyvkGn7rBikvjPw46xz9Plozp4jl7AOsx';
        $store->save();

        $store = new SmPaymentGatewaySetting();
        $store->gateway_name        = 'Paystack'; 
        $store->gateway_secret_key   = 'sk_live_2679322872013c265e161bc8ea11efc1e822bce1';
        $store->gateway_publisher_key  = 'pk_live_e5738ce9aade963387204f1f19bee599176e7a71';
        $store->save(); 
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_payment_gateway_settings');
    }
}
