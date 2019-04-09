<?php

/*Route::group(['namespace' => 'Dorcas\ModulesCustomers\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('sales', 'ModulesCustomersController@index')->name('sales');
});*/

Route::group(['middleware' => ['web'], 'namespace' => 'Dorcas\ModulesCustomers\Http\Controllers', 'prefix' => 'mcu'], function () {
    Route::get('customers-main', 'ModulesCustomersController@main')->name('customers-main');
});

Route::group(['middleware' => ['web'], 'namespace' => 'Dorcas\ModulesCustomers\Http\Controllers', 'prefix' => 'mcu'], function () {
    Route::get('/customers-customers', 'ModulesCustomersController@customers')->name('customers-customers');
    Route::get('/customers-custom-fields', 'ModulesCustomersController@custom_fields')->name('customers-custom-fields');
    Route::get('/groups', 'ModulesCustomersController@groups')->name('customers-groups');
    Route::post('/groups', 'ModulesCustomersController@groups_post')->name('customers-groups-post');
    Route::delete('/groups-delete/{id}', 'ModulesCustomersController@groups_delete');
    Route::get('/customers-search', 'ModulesCustomersController@customers_search')->name('customers-search');
    Route::get('/customers-customers/{id}', 'ModulesCustomersController@customers_view')->name('customers-view');
    Route::post('/customers', 'Customers\Customers@create');
    Route::get('/customers/new', 'Customers\NewCustomer@index')->name('customers-new');
    Route::post('/customers/new', 'Customers\NewCustomer@create');
    Route::post('/customers/{id}', 'Customers\Customer@post');
    Route::delete('/customers', 'Customers\Customers@delete');
});

Route::group(['middleware' => ['web'], 'namespace' => 'Ajax', 'prefix' => 'xhr'], function () {
    Route::get('/crm/custom-fields', 'Crm\CustomFields@search');
    Route::post('/crm/custom-fields', 'Crm\CustomFields@create');
    Route::delete('/crm/custom-fields/{id}', 'Crm\CustomField@delete');
    Route::put('/crm/custom-fields/{id}', 'Crm\CustomField@update');
    Route::delete('/crm/customers/{id}', 'Crm\Customer@delete');
    Route::put('/crm/customers/{id}', 'Crm\Customer@update');
    Route::delete('/crm/customers/{id}/notes', 'Crm\Customer@deleteNote');
    Route::get('/crm/customers/{id}/notes', 'Crm\Customer@readNotes');
    Route::post('/crm/customers/{id}/notes', 'Crm\Customer@addNote');
    
    Route::get('/crm/customers/{id}/deals', 'Crm\Deals@search');
    Route::post('/crm/customers/{id}/deals', 'Crm\Deals@create');
    
    Route::post('/crm/deals/{id}', 'Crm\Deals@delete');
    
    Route::delete('/crm/groups/{id}/customers', 'Crm\Groups@deleteCustomers');
    Route::post('/crm/groups/{id}/customers', 'Crm\Groups@addCustomers');
});



?>