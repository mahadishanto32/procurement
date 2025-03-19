<?php
Route::prefix('/accounts')->as('accounts.')->group(function (){
        
        Route::get('supplier-payments',[
            //'middleware'=>'permission:accounts-list|accounts-create|accounts-edit|accounts-delete',
            'as'=>'supplier.payment.list',
            'uses'=>'Accounts\SupplierPaymentController@index'
        ]);

        Route::post('supplier-payments',[
            //'middleware'=>'permission:accounts-list|accounts-create|accounts-edit|accounts-delete',
            'as'=>'supplier.payment.store',
            'uses'=>'Accounts\SupplierPaymentController@store'
        ]);

        Route::get('billing-list',[
            //'middleware'=>'permission:accounts-po-attachment-list',
            'as'=>'billing.list',
            'uses'=>'Accounts\SupplierPaymentController@billingList'
        ]);

        // Route::post('supplier-ledger-po-wise-generate',[
        //     //'middleware'=>'permission:supplier-ledger-generate',
        //     'as'=>'supplier.ledger.po-wise-generate',
        //     'uses'=>'Accounts\SupplierPaymentController@ledgerPOWiseGenerate'
        // ]);

        // Route::post('supplier-ledger-generate',[
        //     //'middleware'=>'permission:supplier-ledger-generate',
        //     'as'=>'supplier.ledger.generate',
        //     'uses'=>'Accounts\SupplierPaymentController@ledgerGenerate'
        // ]);

        Route::get('accounts-po-invoice-list/{id}',[
            'as'=>'po.invoice.list',
            'uses'=>'Accounts\SupplierPaymentController@poInvoiceList'
        ]);

        Route::get('supplier-ledgers',[
            //'middleware'=>'permission:supplier-ledger-generate',
            'as'=>'supplier.ledger',
            'uses'=>'Accounts\SupplierLedgerController@index'
        ]);
}); 

    //Purchase Order Cash Approval from Financial Department
    Route::get('po-cash-approval','Accounts\CashApprovalController@index')->name('po.cash.approval.list');

    Route::post('po-cash-approval-store','Accounts\CashApprovalController@store')
          ->name('po.cash.approval.store');
    //End
