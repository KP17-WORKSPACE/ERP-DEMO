<?php

namespace Spondonit\Ticket;

use Illuminate\Support\ServiceProvider;




class TicketServiceProvider extends ServiceProvider
{
	public function boot(){
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/views', 'ticket');
		$this->loadMigrationsFrom(__DIR__.'/database/migrations');

	}

	public function register(){
		
	}
	
}
