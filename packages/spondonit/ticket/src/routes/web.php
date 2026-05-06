<?php





Route::group(['middleware' => ['web', 'auth']], function () {

	Route::group(['prefix' => 'infix'], function () {
		Route::group(['namespace' => 'Spondonit\Ticket\Http\Controllers'], function () {
			Route::get('tickets', 'InfixTicketController@index');


			/* Route::get('new_ticket', 'TicketsController@create');
			Route::post('new_ticket', 'TicketsController@store');
			Route::get('my_tickets', 'TicketsController@userTickets');
			Route::get('tickets/{ticket_id}', 'TicketsController@show');
			Route::post('comment', 'CommentsController@postComment'); */
		});
	});
});
