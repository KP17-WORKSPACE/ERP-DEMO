<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmPaymentGatewaySetting extends Model
{
    public static function getStripeDetails(){
    	$stripeDetails = SmPaymentGatewaySetting::select('stripe_api_secret_key', 'stripe_publisher_key')->where('gateway_name', '=', 'Stripe')->first();
    	if(!empty($stripeDetails)){
    		return $stripeDetails->stripe_publisher_key;
    	}
    	
    }
}
