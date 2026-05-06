<?php


Route::group(['middleware' => ['web', 'auth']], function () {

	Route::group(['prefix' => 'infix'], function () {
		Route::group(['namespace' => 'Spondonit\Invoice\Http\Controllers'], function () {


			Route::get('invoice-category', 'InfixInvoiceController@invoiceCategory');
			Route::post('invoice-category-store', 'InfixInvoiceController@invoiceCategoryStore');
			Route::get('invoice-category-edit/{id}', 'InfixInvoiceController@invoiceCategoryEdit');
			Route::post('invoice-category-update', 'InfixInvoiceController@invoiceCategoryUpdate');
			Route::get('invoice-category-delete/{id}', 'InfixInvoiceController@invoiceCategoryDelete');
			Route::get('invoice-category-assign/{id}', 'InfixInvoiceController@invoiceCategoryAssign');
			Route::post('invoice-permission-store', 'InfixInvoiceController@invoicePermissionStore');



			Route::get('invoice-setting', 'InfixInvoiceController@invoiceSetting');
			Route::post('invoice-setting-update', 'InfixInvoiceController@invoiceSettingUpdate');


			Route::get('invoice-create', 'InfixInvoiceController@invoiceCreate');
			Route::post('invoice-store', 'InfixInvoiceController@invoiceStore');
			Route::get('invoice-list', 'InfixInvoiceController@invoiceList');
			Route::get('invoice-view/{id}', 'InfixInvoiceController@invoiceView');
			Route::get('invoice-edit/{id}', 'InfixInvoiceController@invoiceEdit');
			Route::post('invoice-update', 'InfixInvoiceController@invoiceUpdate');

			Route::get('invoice-delete/{id}', 'InfixInvoiceController@invoiceDelete');

			Route::get('invoice-generate/{id}', 'InfixInvoiceController@invoiceGenerate');


			//add payment method 
			Route::post('payment-method-store', 'InfixInvoiceController@paymentMethodStore');


			Route::get('get-receive-item-tender', 'InfixInvoiceController@getReceiveItemTender');
			Route::get('get-receive-item-details', 'InfixInvoiceController@getReceiveItemDetails');
		});
	});
});
