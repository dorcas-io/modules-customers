<?php

/*Route::group(['namespace' => 'Dorcas\ModulesCustomers\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('sales', 'ModulesCustomersController@index')->name('sales');
});*/

Route::group(['middleware' => ['auth'], 'prefix' => 'apps'], function () {
    Route::get('/crm', 'Crm\Crm@index')->name('customers-main');
});

Route::group(['middleware' => ['web'], 'namespace' => 'Dorcas\ModulesCustomers\Http\Controllers', 'prefix' => 'customers'], function () {
    Route::delete('/customers', 'Customers\Customers@delete');
    Route::get('/customers', 'Customers\Customers@index')->name('customers');
    Route::post('/customers', 'Customers\Customers@create');
    Route::get('/customers/new', 'Customers\NewCustomer@index')->name('customers-new');
    Route::post('/customers/new', 'Customers\NewCustomer@create');
    Route::get('/customers/{id}', 'Customers\Customer@index')->name('customers-single');
    Route::post('/customers/{id}', 'Customers\Customer@post');
    Route::get('/groups', 'Groups@index')->name('customers-groups');
    Route::post('/groups', 'Groups@post');
    Route::get('/custom-fields', 'ContactFields\CustomField@index')->name('customers-custom-fields');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Ajax', 'prefix' => 'xhr'], function () {
    Route::get('/crm/custom-fields', 'Crm\CustomFields@search');
    Route::post('/crm/custom-fields', 'Crm\CustomFields@create');
    Route::delete('/crm/custom-fields/{id}', 'Crm\CustomField@delete');
    Route::put('/crm/custom-fields/{id}', 'Crm\CustomField@update');
    Route::get('/crm/customers', 'Crm\Customers@search');
    Route::delete('/crm/customers/{id}', 'Crm\Customer@delete');
    Route::put('/crm/customers/{id}', 'Crm\Customer@update');
    Route::delete('/crm/customers/{id}/notes', 'Crm\Customer@deleteNote');
    Route::get('/crm/customers/{id}/notes', 'Crm\Customer@readNotes');
    Route::post('/crm/customers/{id}/notes', 'Crm\Customer@addNote');
    
    Route::get('/crm/customers/{id}/deals', 'Crm\Deals@search');
    Route::post('/crm/customers/{id}/deals', 'Crm\Deals@create');
    
    Route::post('/crm/deals/{id}', 'Crm\Deals@delete');
    
    Route::delete('/crm/groups/{id}', 'Crm\Groups@delete');
    Route::delete('/crm/groups/{id}/customers', 'Crm\Groups@deleteCustomers');
    Route::post('/crm/groups/{id}/customers', 'Crm\Groups@addCustomers');
});



?>