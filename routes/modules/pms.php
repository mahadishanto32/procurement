<?php


Route::group(['prefix' => 'pms','namespace' => 'Pms','as'=>'pms.', 'middleware' => 'auth'], function(){

    Route::get('/', 'DashboardController@index')->name('dashboard');
    //warehouse
    Route::resource('/warehouse', 'WarehouseController');//->middleware('permission:warehouse-list|warehouse-create|warehouse-edit|warehouse-delete');
    //supplier
    Route::resource('/payment-terms', 'PaymentTermController');//->middleware('permission:payment-terms');
    Route::resource('/supplier', 'SupplierController');//->middleware('permission:supplier-list|supplier-create|supplier-edit|supplier-delete');
    Route::post('/supplier/{id}/toggle', 'SupplierController@toggle');

    Route::resource('/return-faq', 'Grn\FaqController');//->middleware('permission:faq-list|faq-create|faq-edit|faq-delete');


    Route::get('/supplier/profile/{supplierId}', 'SupplierController@showSupplierProfile')->name('supplier.profile');

    Route::get('/supplier/rating/{supplierId}/{grnId}', 'SupplierController@showSupplierRatingFrom')->name('supplier.rating');
    Route::post('/supplier/rating', 'SupplierController@storeSupplierRating')->name('supplier.rating.store');

    Route::get('/suppliers/import-excel', 'Import\SupplierUploadController@showSupplierImportForm')->name('suppliers.import-excel');
    Route::post('/suppliers/import-excel', 'Import\SupplierUploadController@storeSupplierData')->name('suppliers.import-excel');
    
    Route::get('/supplier/{supplier_id}/create-supplier-log', 'SupplierController@createSupplierLog');
    Route::post('/supplier/{supplier_id}/save-supplier-log', 'SupplierController@saveSupplierLog');
    Route::get('/supplier/{log_id}/edit-supplier-log', 'SupplierController@editSupplierLog');
    Route::post('/supplier/{log_id}/update-supplier-log', 'SupplierController@updateSupplierLog');
    Route::post('/supplier/{log_id}/delete-supplier-log', 'SupplierController@deleteSupplierLog');


    Route::prefix('/requisition')->as('requisition.')->group(function (){ 

        Route::resource('type', 'Requisition\RequisitionTypeController');
        Route::resource('/requisition', 'RequisitionController')->middleware('permission:requisition-list|requisition-create|requisition-edit|requisition-delete');
        Route::get('/halt-requisition', 'RequisitionController@halt')->middleware('permission:halt-requisitions|requisition-delete');

        Route::get('/load-project-wise-deliverables/{project?}', 'RequisitionController@loadProjectWiseDeliverables')->name("load-project-wise-deliverables");

        Route::get('/requisition/load-category-wise-product/{categoryId}', 'RequisitionController@loadCategoryWiseProducts');

        Route::get('/requisition/load-category-wise-subcategory/{categoryId}', 'RequisitionController@loadCategoryWiseSubcategory');

        Route::get('/list-view', 'RequisitionController@requisitionListView')
        ->name('list.view.index');
        /*->middleware('permission:requisition-list-view');*/

        Route::get('/list-view-show/{id}', 'RequisitionController@showRequisition')
        ->name('list.view.show');

        Route::post('/list-view-search', 'RequisitionController@requisitionListViewSearch')
        ->name('list.view.search');

        Route::post('/approved-status', 'RequisitionController@toggleRequisitionStatus')
            ->name('list.view.approved.status');

        Route::post('/halt-status', 'RequisitionController@haltRequisitionStatus')->name('halt.status');

        //Tracking list
        Route::post('/tracking-show','RequisitionController@showTracking');

        Route::get('notification-all','RequisitionController@notificationAll')
              ->name('view.all.notification');

        Route::post('/mark-as-read','RequisitionController@markAsRead');
        //Delivered Requisiton
        Route::get('delivered-requistion-list','RequisitionController@deliveredRequisitionList')->name('delivered.requistion.list');

        Route::post('delivered-requistion-ack','RequisitionController@deliveredRequisitionAck');

        Route::post('delivered-requistion-search','RequisitionController@deliveredRequisitionSearch')
            ->name('delivered.requistion.search');
    });

    Route::prefix('/store-manage')->as('store-manage.')->group(function (){

        Route::get('/store-requistion', 'StoreController@storeRequisitionListView')
            ->name('store-requistion-list');//->middleware('permission:store-requisition');

        Route::post('/store-requistion-search', 'StoreController@storeRequisitionListViewSearch')
            ->name('store-requistion-search');

        Route::get('store-inventory-compare/{id}', 'StoreController@storeInventoryCompare')->name('store.inventory.compare');

        //confirm delivery form
        Route::get('store-confirm-delivery/{id}', 'StoreController@confirmDelivery')
            ->name('store-requistion.delivery');
        //confirm delivery submit
        Route::post('store-confirm-delivery-submit', 'StoreController@confirmDeliverySubmit')
            ->name('store-requistion.submit');

        Route::get('delivered-requisition/{deliveryStatus?}', 'StoreController@index')
            ->name('delivered-requisition');

        Route::get('delivered-list', 'StoreController@deliveredList')
            ->name('store.deliverd.requistion.list');

        Route::post('store-delivered-requistion-ack','StoreController@deliveredRequisitionAck');

        Route::post('store-delivered-requistion-search','StoreController@deliveredRequisitionSearch')
            ->name('store.delivered.requistion.search');

        Route::post('store-delivered-requistion-view','StoreController@requisitionDeliveryView');

        Route::get('requisition-delivered-list/{requisitionId?}', 'RequisitionDeliveryController@requisitionDeliveryList')
            ->name('requisition-delivered-list');

        Route::get('requisition-delivered-detail/{requisitionDeliveryId?}', 'RequisitionDeliveryController@requisitionDeliveryDetail')
            ->name('requisition-delivered-detail');

        //From store department to purchase department
        Route::post('send-to-purchase-department', [
            'as' => 'send.to.purchase.department',
            'uses' => 'StoreController@purchaseDepartment'
        ]);
        //From store department pending delivery
        Route::post('change-action-to-rfp', [
            'as' => 'change.action.to.rfp',
            'uses' => 'StoreController@changeActionToRFP'
        ]);

        Route::post('department-wise-employee', [
            'as' => 'department.wise.employee',
            'uses' => 'StoreController@departmentWiseEmployee'
        ]);


        Route::get('store-rfp-requistion-list', [
            'as' => 'rfp.requisition.list',
            'uses' => 'StoreController@rfpRequisitionList'
        ]);

        Route::post('store-rfp-requistion-search', [
            'as' => 'rfp.requisition.search',
            'uses' => 'StoreController@rfpRequisitionSearch'
        ]);

        Route::post('rfp-department-wise-employee', [
            'as' => 'rfp.department.wise.employee',
            'uses' => 'StoreController@rfpDepartmentWiseEmployee'
        ]);

        //notification send
        Route::get('requisition-item-list/{id}', [
            'as' => 'requisition.items.list',
            'uses' => 'StoreController@requisitionItemsList'
        ]);

        Route::post('notification-send-to-users', [
            'as' => 'notification.send.to.users',
            'uses' => 'StoreController@sendNotificationToUsers'
        ]);
    });

    Route::prefix('/rfp')->as('rfp.')->group(function (){
        Route::resource('/request-proposal', 'RequestProposalController');//->middleware('permission:rfp-list|rfp-create|rfp-edit|rfp-delete');

        Route::get('request-proposal/create/separate','RequestProposalController@createSeparate');

        Route::post('request-proposal/separate/store',[
            'as'=>'request-proposal.separate.store',
            'uses'=>'RequestProposalController@storeSeparate'
        ]);

        Route::get('/request-proposal/details/{product_id}', 'RequestProposalController@requisitionDetailByProductId');

        Route::get('/requisitions', 'RequestProposalController@requisitionIndex')
        ->name('requisitions.list');//->middleware('permission:rfp-requisitions-list');

        Route::post('/rfp-requistion-search','RequestProposalController@rfpRequisitionListViewSearch')
              ->name('rfp-requistion-search');

        Route::get('/store-inventory-compare/{id}', 'StoreController@storeInventoryCompare');//->middleware('permission:inventory-compare');
        Route::post('/convert-to-rfp', 'RequestProposalController@convertToRfp')
        ->name('convert.to.rfp');

        //puchase delivery
        Route::get('send-to-purchase/{id}', 'RequestProposalController@sendToPurchase')
        ->name('send.to.purchase');

        Route::get('get-supplier-payment-terms/{id}', 'RequestProposalController@getSupplierPaymentTerms');

        //puchase delivery submit
        Route::post('send-to-purchase-submit', 'RequestProposalController@sendToPurchaseSubmit')
        ->name('store-requistion.purchase');

        //puchase delivery submit
        Route::post('rfp-quotation-generate-complete', 'RequestProposalController@rfpQuotationgenerateComplete')
        ->name('generate.complete');

        //Quotations
        Route::resource('/quotations', 'QuotationController');
        //->middleware('permission:quotations-list|quotations-create|quotations-edit|quotations-delete');

        Route::get('quotations/generate/{id}', [
            'as' => 'quotations.generate',
            'uses' => 'QuotationController@quotationGenerate'
        ]);

    });

    Route::prefix('/inventory')->as('inventory.')->group(function (){
        Route::resource('/inventory-stock-report','InventoryStockController');
        //->middleware('permission:inventory-stock-report');
        
        Route::resource('/inventory-summary','InventorySummaryController');
        //->middleware('permission:inventory-summary-list|inventory-summary-create|inventory-summary-edit|inventory-summary-delete');

        Route::get('inventory-summary/{category_id}/get-products','InventorySummaryController@getProduct');
        Route::get('warehouse-wise-product-inventory-details/{productId}',
            'InventorySummaryController@warehouseWiseProductInventoryDetails'
        );

        Route::get('/inventory-logs', [
            'as' => 'inventory.logs',
            'middleware'=>'permission:inventory-logs',
            'uses' => 'InventoryLogsController@index'
        ]);
    });

    Route::prefix('/product-management')->as('product-management.')->group(function (){

        Route::resource('/brand', 'BrandController');//->middleware('permission:brand-list|brand-create|brand-edit|brand-delete');
        Route::post('/brand/import', 'BrandController@importBrand')->name('brand.import');
        Route::resource('/category', 'CategoryController');//->middleware('permission:category-list|category-create|category-edit|category-delete');
        Route::resource('/sub-category', 'SubCategoryController');//->middleware('permission:category-list|category-create|category-edit|category-delete');
        Route::post('/category-import', 'CategoryController@importCategory')->name('category.import');
        Route::post('/sub-category/{id}/update-attributes', 'SubCategoryController@updateAttributes');

        Route::resource('product-unit','ProductUnitController');//->middleware('permission:porduct-unit-list|porduct-unit-create|porduct-unit-edit|porduct-unit-delete');

        Route::resource('/product','ProductController');//->middleware('permission:product-list|product-create|product-edit|product-delete');
        Route::get('/product-import-sample','ProductController@importProductSample');
        Route::post('/product/import','ProductController@importProduct')->name('product.import');

        Route::resource('/attributes','AttributeController');//->middleware('permission:attributes|attribute-create|attribute-edit|attribute-delete');

        Route::resource('/attribute-options','AttributeOptionController');//->middleware('permission:attribute-options|attribute-option-create|attribute-option-edit|attribute-option-delete');

        Route::resource('/product-models','ProductModelController');//->middleware('permission:product-models|product-model-create|product-model-edit|product-model-delete');


        //Route::get('product-category','ProductController@proCat');
    });

    Route::prefix('/quotation')->as('quotation.')->group(function (){

       Route::get('/index', [
            'as' => 'quotations.index',
            'uses' => 'QuotationController@index'
        ]);
       Route::get('quotation-items/{id}','QuotationController@quotationItems');

       Route::get('/cs-analysis', [
            'as' => 'quotations.cs.analysis',
            'uses' => 'QuotationController@analysisIndex'
        ]);

       Route::get('/cs-compare/{id}', [
            'as' => 'quotations.cs.compare',
            'uses' => 'QuotationController@compareGridView'
        ]);

       Route::get('/cs-compare-list/{id}', [
            'as' => 'quotations.cs.compare.list',
            'uses' => 'QuotationController@compareListView'
        ]);

       Route::get('/cs-compare-view/{id}/{slug}', [
            'as' => 'quotations.cs.compare.view',
            'uses' => 'QuotationController@compareView'
        ]);


        Route::get('/request-proposal-details/{id}', [
            'as' => 'quotations.cs.proposal.details',
            'uses' => 'QuotationController@proposalDetailsView'
        ]);

       Route::post('/cs-compare/store', [
            'as' => 'quotations.cs.compare.store',
            'uses' => 'QuotationController@compareStore'
        ]);

       Route::post('/cs-compare/approved', [
            'as' => 'quotations.cs.compare.approved',
            'uses' => 'QuotationController@approved'
        ]);

       Route::post('approved-status','QuotationController@toggleQuotationStatus');

       Route::post('/halt-status', [
            'as' => 'halt.status',
            'uses' => 'QuotationController@haltStatus'
        ]);

        Route::post('/approved-view-search', [
            'as' => 'approved.view.search',
            'uses' => 'QuotationController@search'
        ]);

       Route::get('approval-list',[
                //'middleware'=>'permission:quotations-approval-list',
                'as'=>'approval.list',
                'uses'=>'QuotationController@approvalList'
       ]);

       Route::get('/generate-po-list', [
            'as' => 'quotations.generate.po.list',
            'uses' => 'QuotationController@generatePoList'
        ]);

       Route::post('/generate-po-store', [
            'as' => 'quotations.generate.po.store',
            'uses' => 'QuotationController@generatePoStore'
        ]);
       
       Route::get('generate-po-process/{id}',[
        'as'=>'generate.po.process',
        'uses'=>'QuotationController@generatePoProcess'
       ]);

       Route::get('unit-wise-requisition/{hrUnitId}/{quotationId}','QuotationController@unitWiseRequisition');
       Route::post('requisition-wise-item-qty','QuotationController@requisitionWiseItemsQty');
       Route::post('complete-quotation','QuotationController@completeQuotation');

    });

    Route::group(['prefix' => 'admin','as'=>'admin.'], function() {
        Route::resource('users','UserController');//->middleware('permission:user-list|user-create|user-edit|user-delete');
        Route::get('deleted-users','UserController@deleted')->name('users.deleted');//->middleware('permission:user-list|user-create|user-edit|user-delete');
        Route::get('restore-user/{id}','UserController@restore');//->middleware('permission:user-list|user-create|user-edit|user-delete');
        Route::get('/users-data','UserController@usersDataLoad');

    });

    Route::group(['prefix' => 'acl','as'=>'acl.'], function() {

        Route::resource('roles','Spatie\RoleController');//->middleware('permission:role-list|role-create|role-edit|role-delete');
        Route::get('/roles-data','Spatie\RoleController@rolesData');
        Route::resource('permission','Spatie\AclPermissionController');//->middleware('permission:permission-list|permission-create|permission-edit|permission-delete');
        Route::get('/approval', 'Spatie\AclPermissionController@approvalSettings')->name('approval-settings');

        Route::resource('menu','Menu\MenuController');//->middleware('permission:menu-list|menu-create|menu-edit|menu-delete');
        Route::resource('sub-menu','Menu\SubMenuController');//->middleware('permission:sub-menu-list|sub-menu-create|sub-menu-edit|sub-menu-delete');
        //Route::resource('sub-sub-menu','Menu\SubSubMenuController');//->middleware('permission:menu');
        Route::resource('project-menu','\App\Http\Controllers\Myproject\Menu\ProjectMenuController');//->middleware('permission:menu-list|menu-create|menu-edit|menu-delete');
        Route::resource('project-sub-menu','\App\Http\Controllers\Myproject\Menu\ProjectSubMenuController');//->middleware('permission:sub-menu-list|sub-menu-create|sub-menu-edit|sub-menu-delete');
        //Route::resource('project-sub-sub-menu','App\Myproject\Menu\ProjectSubSubMenuController');//->middleware('permission:menu');

        Route::resource('accounting-menu','\App\Http\Controllers\Myaccounting\Menu\AccountsMenuController');//->middleware('permission:accounts-menu-list|accounts-menu-create|accounts-menu-edit|accounts-menu-delete');
        Route::resource('accounting-sub-menu','\App\Http\Controllers\Myaccounting\Menu\AccountsSubMenuController');//->middleware('permission:accounts-sub-menu-list|accounts-sub-menu-create|accounts-sub-menu-edit|accounts-sub-menu-delete');
    });

    Route::prefix('/purchase')->as('purchase.')->group(function (){
       
         Route::get('order-list',[
            //'middleware'=>'permission:po-list',
            'as'=>'order-index',
            'uses'=>'Purchase\PurchaseController@orderIndex'
        ]);

        //view quotation details
        Route::get('quotation-items/{quotation_id}','QuotationController@quotationItems');
        Route::get('/mail/{order}','Purchase\PurchaseController@sendMailToSupplier')->name('send-mail');
        Route::get('order-list/{id}/show',[
            //'middleware'=>'permission:purchase-order-show',
            'as'=>'order-list.show',
            'uses'=>'Purchase\PurchaseController@show'
        ]);
    });

    //GRN Route
     Route::prefix('/grn')->as('grn.')->group(function (){

        Route::get('/po-list','Grn\GRNController@poListIndex')->name('po.list');
        // ->middleware('permission:grn-po-list');

        Route::post('/po-list-search', 'Grn\GRNController@poListSearch')
              ->name('po.list.search');


        Route::get('/grn-list/create/{id}','Grn\GRNController@createGRN')->name('grn-list.createGRN');
        // ->middleware('permission:grn-add|grn-generate');

        Route::get('/po-wise-grn-list','Grn\GRNController@purchaseOrderListAgainstGrn')->name('po-wise-grn-list.purchaseOrderListAgainstGrn');
        // ->middleware('permission:po-wise-grn-list');

        Route::resource('grn-process','Grn\GRNController');
        // ->middleware('permission:grn-list|grn-add|grn-edit|grn-delete|grn-search');

        Route::post('/grn-process-search', 'Grn\GRNController@grnProcessSearch')
              ->name('grn-process.search');

        Route::resource('/gate-in-slip', 'Grn\GateInSlipController');
     });

    Route::get('qce-list','Grn\GRNStockInController@index')->name('stockin.grn.list');
    Route::get('grn-list','Grn\GRNStockInController@grnList')->name('grn.list');
    Route::get('grn-slip/{id}','Grn\GRNStockInController@grnSlip')->name('grn.slip');
    Route::get('grn-stock-in-list/{id}','Grn\GRNStockInController@grnStockInList')->name('grn.stock.in.list');


    Route::post('grn-stock-in-list-store','Grn\GRNStockInController@store')->name('grn.stock.in.store');

    Route::prefix('/quality-ensure')->as('quality.')->group(function (){

        Route::get('ensure-check/{id}','Quality\QualityEnsureController@ensureCheck')->name('ensure.check');
        //->middleware('permission:quality-ensure');

        Route::get('ensure-check-get-faqs/{id}','Quality\QualityEnsureController@getFaqs');
        //->middleware('permission:quality-ensure');
        

        Route::post('ensure-status-save','Quality\QualityEnsureController@save')->name('ensure.status.save');
        //->middleware('permission:quality-ensure-approved|quality-ensure-return|quality-ensure-return-change');

        Route::get('approved-list','Quality\QualityEnsureController@index')->name('ensure.approved.list');
        //->middleware('permission:quality-ensure-approval-list');
        Route::get('approved-list/{id}','Quality\QualityEnsureController@grnWiseApprovedItemList')
                ->name('ensure.approved.single.list');

        Route::get('return-list','Quality\QualityEnsureController@returnlList')->name('ensure.return.list');
        //->middleware('permission:quality-ensure-return-list');
        Route::get('return-list/{id}','Quality\QualityEnsureController@grnWiseReturnItemList')
            ->name('ensure.return.single.list');


        Route::get('return-change-list','Quality\QualityEnsureController@returnChangeList')->name('ensure.return.change.list');
        //->middleware('permission:quality-ensure-return-change-list');
        Route::get('return-change-list/{id}','Quality\QualityEnsureController@grnWiseReturnChangeItemList')
                ->name('ensure.return.change.single.list');
        //->middleware('permission:quality-ensure-return-change-received-list');

        Route::post('return-change-save','Quality\QualityEnsureController@returnChangeReceived')->name('ensure.return.change.received');

        //Approved
        Route::get('approved-item-print-view/{id}/{type}',[
            'as'=>'approved.item.print',
            'uses'=>'Quality\QualityEnsureController@approvedItemPrint'
        ]);
        //Return
        Route::get('return-item-print-view/{id}/{type}',[
            'as'=>'return.item.print',
            'uses'=>'Quality\QualityEnsureController@returnItemPrintView'
        ]);
        //Return Replace
        Route::get('return-replace-item-print-view/{id}/{type}',[
            'as'=>'return.replace.item.print',
            'uses'=>'Quality\QualityEnsureController@returnReplaceItemPrintView'
        ]);

    });

    Route::prefix('/billing-audit')->as('billing-audit.')->group(function (){
        
        Route::get('po-list',[
            //'middleware'=>'permission:upload-po-attachment',
            'as'=>'po.list',
            'uses'=>'BillingController@index'
        ]);

        Route::post('po-list-attachment-upload',[
            //'middleware'=>'permission:upload-po-attachment',
            'as'=>'po.list.attachment-upload',
            'uses'=>'BillingController@attachmentUploadForm'
        ]);

        Route::get('po-invoice-list/{id}',[
            'as'=>'po.invoice.list',
            'uses'=>'BillingController@poInvoiceList'
        ]);

        Route::get('audit-po-invoice-list/{id}',[
            'as'=>'audit.po.invoice.list',
            'uses'=>'BillingController@auditPoInvoiceList'
        ]);

        Route::post('po-attachment-upload',[
            'as'=>'po.attachment.upload',
            'uses'=>'BillingController@attachmentUpload'
        ]);

        Route::post('grn-attachment-upload',[
            'as'=>'grn.attachment.upload',
            'uses'=>'BillingController@grnAttachmentUpload'
        ]);

        Route::get('po-invoice-print/{id}',[
            'as'=>'po.invoice.print',
            'uses'=>'BillingController@poInvoicePrint'
        ]);

        Route::get('billing-po-attachment-list',[
            'as'=>'po.attachment.list',
            'uses'=>'BillingController@billingPOAttachmentList'
        ]);

        Route::post('po-invoice-approved', 'BillingController@billingUpdateAction')
                ->name('po.invoice.approved');

    }); 
     // approval range setup
    Route::resource('/range-setup','ApprovalRangeSetupController');
    @include('accounts.php');
});


