<?php

namespace Spondonit\Invoice;

use Illuminate\Support\ServiceProvider;




class InvoiceServiceProvider extends ServiceProvider
{
	public function boot(){
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/views', 'invoice');
		$this->loadMigrationsFrom(__DIR__.'/database/migrations');

	}

	public function register(){
		
	}
	
}
