<?php 
Route::group(['prefix' => 'pms','namespace' => 'Pms'], function(){
	Route::get('accounts/supplier-ledgers','API\SupplierLedgerController@supplierLedgers');
});


