<?php
Route::prefix('/accounting')->as('accounting.')->group(function (){
    Route::resource('/','Myaccounting\DashboardController');

    //--------Chart of accounts Start-------//
    Route::resource('companies','Myaccounting\CompanyController');//->middleware('permission:companies|company-create|company-edit|company-delete');

    Route::resource('cost-centres','Myaccounting\CostCentreController');//->middleware('permission:cost-centres|cost-centre-create|cost-centre-edit|cost-centre-delete');

    Route::resource('account-groups','Myaccounting\AccountGroupsController');//->middleware('permission:account-groupss|account-groups-create|account-groups-edit|account-groups-delete');
    Route::resource('chart-of-accounts','Myaccounting\ChartOfAccountsController');//->middleware('permission:chart-of-accounts|chart-of-accounts-create|chart-of-accounts-edit|chart-of-accounts-delete');
    //--------Chart of accounts Start-------//

    //--------Settings Start-------//
    Route::resource('entry-types','Myaccounting\EntryTypesController');//->middleware('permission:entry-types|entry-type-create|entry-type-edit|entry-type-delete');

    Route::resource('tags','Myaccounting\TagsController');//->middleware('permission:tags|tag-create|tag-edit|tag-delete');

    Route::resource('bank-accounts','Myaccounting\BankAccountsController');//->middleware('permission:bank-accounts|bank-account-create|bank-account-edit|bank-account-delete');

    Route::resource('fiscal-years','Myaccounting\FiscalYearsController');//->middleware('permission:fiscal-years|fiscal-year-create|fiscal-year-edit|fiscal-year-delete');
    //--------Settings End-------//

    //--------Entries Start-------//
    Route::resource('entries','Myaccounting\EntryController');//->middleware('permission:entries|entry-create|entry-view|entry-edit|entry-delete');
    //--------Entries End-------//

    //--------Reports Start-------//
    Route::resource('balance-sheet','Myaccounting\BalanceSheetController');//->middleware('permission:balance-sheet|balance-sheet-print|balance-sheet-pdf|balance-sheet-excel');

    Route::resource('profit-loss','Myaccounting\ProfitLossController');//->middleware('permission:profit-loss|profit-loss-print|profit-loss-pdf|profit-loss-excel');

    Route::resource('trial-balance','Myaccounting\TrialBalanceController');//->middleware('permission:trial-balance|trial-balance-print|trial-balance-pdf|trial-balance-excel');

    Route::resource('ledger-statement','Myaccounting\LedgerStatementController');//->middleware('permission:ledger-statement|ledger-statement-print|ledger-statement-pdf|ledger-statement-excel');

    Route::resource('ledger-entries','Myaccounting\LedgerEntriesController');//->middleware('permission:ledger-entries|ledger-entries-print|ledger-entries-pdf|ledger-entries-excel');

    Route::resource('reconciliation','Myaccounting\ReconciliationController');//->middleware('permission:reconciliation');
    //--------Reports End-------//
}); 
