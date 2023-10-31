<?php

/*Route::group(['namespace' => 'Dorcas\ModulesCustomers\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('sales', 'ModulesCustomersController@index')->name('sales');
});*/

Route::group(['middleware' => ['auth','web'], 'namespace' => 'Dorcas\ModulesCustomers\Http\Controllers', 'prefix' => 'mcu'], function () {
    Route::get('/customers-main', 'ModulesCustomersController@main')->name('customers-main');
    Route::get('/customers-customers', 'ModulesCustomersController@customers')->name('customers-customers');
    Route::get('/customers-custom-fields', 'ModulesCustomersController@custom_fields')->name('customers-custom-fields');
    Route::get('/customers-groups', 'ModulesCustomersController@groups')->name('customers-groups');
    Route::post('/customers-groups', 'ModulesCustomersController@groups_post')->name('customers-groups-post');
    Route::delete('/groups-delete/{id}', 'ModulesCustomersController@groups_delete');
    Route::get('/customers-search', 'ModulesCustomersController@customers_search')->name('customers_search');
    Route::get('/customers-customers/{id}', 'ModulesCustomersController@customers_view');
    Route::put('/customers-customers/{id}', 'ModulesCustomersController@customers_update');
    Route::post('/customers-customers/{id}', 'ModulesCustomersController@customers_post');
    Route::post('/customers-groups/{id}', 'ModulesCustomersController@groups_customers_add');
    Route::delete('/customers-groups/{id}', 'ModulesCustomersController@groups_customers_delete');
    Route::post('/customers-notes/{id}', 'ModulesCustomersController@notes_customers_add');
    Route::delete('/customers-notes/{id}', 'ModulesCustomersController@notes_customers_delete');
    Route::get('/customers-notes/{id}', 'ModulesCustomersController@notes_customers_read');
    Route::delete('customers-customers/{id}', 'ModulesCustomersController@customers_delete');
    Route::get('/customers-new', 'ModulesCustomersController@customers_new')->name('customers-new');
    Route::post('/customers-new', 'ModulesCustomersController@customers_create');
    Route::post('/customers-custom-fields', 'ModulesCustomersController@custom_fields_create');
    Route::delete('/customers-custom-fields/{id}', 'ModulesCustomersController@custom_fields_delete');
    Route::put('/customers-custom-fields/{id}', 'ModulesCustomersController@custom_fields_update');
});

// Route::group(['middleware' => ['web'], 'namespace' => 'Ajax', 'prefix' => 'xxhr'], function () {
//     Route::get('/crm/custom-fields', 'Crm\CustomFields@search');
//     Route::get('/crm/customers/{id}/deals', 'Crm\Deals@search');
//     Route::post('/crm/customers/{id}/deals', 'Crm\Deals@create');
//     Route::post('/crm/deals/{id}', 'Crm\Deals@delete');
// });



?>