<?php

   //Install for Demo
   Route::get('/install', 'InstallController@index');


   Route::get('/install1', 'InstallController@installPage1'); //envato verification view page



   Route::get('/install2', 'InstallController@installPage2');// if verified, then success message & database credentials page



   Route::get('/install3', 'InstallController@installPage3');
   Route::get('/install4', 'InstallController@installPage4');
   Route::post('/installStep1', 'InstallController@installStep1')->name('installStep1');
   Route::post('/installStep2', 'InstallController@installStep2')->name('installStep2');
   Route::post('/installStep3', 'InstallController@installStep3')->name('installStep3');
   Route::post('/installStep4', 'InstallController@installStep4')->name('installStep4'); 
?>