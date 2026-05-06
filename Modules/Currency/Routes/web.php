<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('currency')->group(function() {
    Route::get('manage-currency', 'CurrencyController@manageCurrency');
    Route::post('currency-store', 'CurrencyController@storeCurrency')->name('storeCurrency');
    Route::post('currency-update', 'CurrencyController@storeCurrencyUpdate')->name('storeCurrencyUpdate');
    Route::get('manage-currency/edit/{id}', 'CurrencyController@manageCurrencyEdit')->name('currency_edit');
    Route::get('manage-currency/delete/{id}', 'CurrencyController@manageCurrencyDelete')->name('currency_delete');

});
