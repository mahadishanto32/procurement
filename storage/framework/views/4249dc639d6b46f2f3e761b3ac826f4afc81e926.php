
<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
<style type="text/css">
    .col-form-label{
        font-size: 14px;
        font-weight: 600;
    }

    .list-unstyled .ratings {
        display: none;
    }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
<?php
use App\Models\PmsModels\Purchase\PurchaseOrderAttachment;
?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="<?php echo e(route('pms.dashboard')); ?>"><?php echo e(__('Home')); ?></a>
                </li>
                <li>
                    <a href="#">PMS</a>
                </li>
                <li class="active">Bill Manage</li>
                <li class="active"><?php echo e(__($title)); ?></li>

            </ul>
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <form action="<?php echo e(route('pms.billing-audit.po.list')); ?>" method="get" accept-charset="utf-8">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="search_text">Enter Search Text</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="search_text" id="search_text" class="form-control" placeholder="Search Here..." value="<?php echo e(request()->has('search_text') ? request()->get('search_text') : ''); ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="from_date"><?php echo e(__('From Date')); ?>:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="from_date" id="from_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(request()->has('from_date')? request()->get('from_date'):date("d-m-Y", strtotime(date('Y-m-01')))); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <p class="mb-1 font-weight-bold"><label for="to_date"><?php echo e(__('To Date')); ?>:</label></p>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="text" name="to_date" id="to_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(request()->has('to_date')? request()->get('to_date'):date('d-m-Y')); ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <p class="mb-1 font-weight-bold"><label for=""></label></p>
                                            <div class="input-group input-group-md">
                                                <button type="submit" class="btn btn-success btn-block rounded mt-8"><i class="las la-search"></i>&nbsp;Search</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <p class="mb-1 font-weight-bold"><label for=""></label></p>
                                            <div class="input-group input-group-md">
                                                <a href="<?php echo e(route('pms.billing-audit.po.list')); ?>" class="btn btn-danger btn-block rounded mt-8"><i class="las la-times"></i>&nbsp;Clear</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                        </form>                  
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%"><?php echo e(__('SL')); ?></th>
                                    <th><?php echo e(__('P.O. Date')); ?></th>
                                    <th><?php echo e(__('Supplier')); ?></th>
                                    <th style="width:10% !important"><?php echo e(__('Reference No')); ?></th>
                                    <th><?php echo e(__('P.O Qty')); ?></th>
                                    <th class="text-center"><?php echo e(__('GRN Qty')); ?></th>
                                    <th class="text-center"><?php echo e(__('Po Amount')); ?></th>
                                    <th class="text-center"><?php echo e(__('GRN Amount')); ?></th>
                                    <th class="text-center"><?php echo e(__('Bill Amount')); ?></th>
                                    <th class="text-center"><?php echo e(__('Bill Number')); ?></th>
                                    <th class="text-center"><?php echo e(__('Status')); ?></th>
                                    <th class="text-center"><?php echo e(__('Attachment')); ?></th>
                                    <th class="text-center"><?php echo e(__('Invoice')); ?></th>
                                    <th class="text-center"><?php echo e(__('Vat')); ?></th>
                                    <th class="text-center"><?php echo e(__('Option')); ?></th>
                                </tr>
                            </thead>
                            <tbody id="viewResult">
                                <?php if(count($purchaseOrdersAgainstGrn)>0): ?>
                                <?php $__currentLoopData = $purchaseOrdersAgainstGrn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                $po_attachment=PurchaseOrderAttachment::where(['purchase_order_id'=>$values->id,'bill_type'=>'po'])->first();
                                ?>
                                <?php if($values->relGoodsReceivedItemStockIn()->where('is_grn_complete','yes')->sum('total_amount') > 0): ?>
                                <tr>
                                    <td style="width:2% !important"><?php echo e($key + 1); ?></td>
                                    <td style="width:8% !important"><?php echo e(date('d-m-Y',strtotime($values->po_date))); ?></td>
                                    <td><?php echo e($values->relQuotation?$values->relQuotation->relSuppliers->name:''); ?></td>
                                    <td> <a href="javascript:void(0)" class="btn-link showPODetails" data-src="<?php echo e(route('pms.purchase.order-list.show',$values->id)); ?>"><?php echo e($values->reference_no); ?></a></td>

                                    <td><?php echo e($values->relPurchaseOrderItems->sum('qty')); ?></td>
                                    <td class="text-center"><?php echo e($values->total_grn_qty); ?></td>
                                    <td class="text-center"><?php echo e(number_format($values->gross_price,2)); ?></td>
                                    <td class="text-center">
                                        <?php if($values->relGoodsReceivedItemStockIn): ?>

                                        <?php echo e(number_format($values->relGoodsReceivedItemStockIn()->where('is_grn_complete','yes')->sum('total_amount'),2)); ?>

                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <?php echo e(PurchaseOrderAttachment::where('purchase_order_id',$values->id)->sum('bill_amount') > 0 ? number_format(PurchaseOrderAttachment::where('purchase_order_id',$values->id)->sum('bill_amount'), 2)  : 'Not Updated Yet'); ?>

                                    </td>

                                    <td><?php echo e(isset($po_attachment->bill_number) ? $po_attachment->bill_number : ''); ?></td>

                                    <td class="text-center text-left">
                                        <?php if($values->relPurchaseOrderItems->sum('qty')==$values->total_grn_qty??0): ?>
                                        <button class="btn btn-default"><?php echo e(__('Full Received')); ?></button>
                                        <?php else: ?>
                                        <button class="btn btn-default"><?php echo e(__('Partial Received')); ?></button>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if(checkPoAttachment($values->id, "po")): ?>

                                        <a href="javascript:void(0)" class="btn btn-info btn-xs UploadPOAttachment" data-id="<?php echo e($values->id); ?>"><i class="las la-upload"></i>Upload
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(!empty($po_attachment->invoice_file)): ?>
                                        <a href="<?php echo e(asset($po_attachment->invoice_file)); ?>" target="__blank" class="btn btn-success btn-xs">
                                            <i class="las la-file-invoice"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                         <?php if(!empty($po_attachment->vat_challan_file)): ?>
                                        <a href="<?php echo e(asset($po_attachment->vat_challan_file)); ?>" class="btn btn-success btn-xs" title="Click here to view vat chalan" target="__blank">
                                            <i class="las la-money-check-alt"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center" style="width:10% !important">
                                        <a href="<?php echo e(route('pms.billing-audit.po.invoice.list',$values->id)); ?>" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Click here to view PO Challan" >Chalan(<?php echo e($values->relGoodReceiveNote->count()); ?>) </a>

                                        <a target="__blank" href="<?php echo e(route('pms.billing-audit.po.invoice.print',$values->id)); ?>" class="btn btn-success btn-xs"><i class="las la-print"></i></a>
                                        <?php if(!empty($po_attachment->status) &&  !empty($po_attachment->remarks)): ?>
                                        <a  po-attachment="<?php echo e(strip_tags($po_attachment->remarks)); ?>" class="viewRemarks btn btn-success btn-xs"><i class="las la-eye"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="la-1x pull-right">
                                    <?php if(count($purchaseOrdersAgainstGrn)>0): ?>
                                    <ul>
                                        <?php echo e($purchaseOrdersAgainstGrn->links()); ?>

                                    </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="POdetailsModel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Purchase Order</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="body">

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<div class="modal" id="PurchaseOrderAttachmentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Purchase Order Attachment</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <?php echo Form::open(['route' => 'pms.billing-audit.po.attachment.upload',  'files'=> true, 'id'=>'', 'class' => 'form-horizontal']); ?>


            <div class="modal-body">

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <?php echo Form::close(); ?>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>

<script>
    (function ($) {
        "use script";

        const showPODetails = () => {
            $('.showPODetails').on('click', function () {

                $.ajax({
                    url: $(this).attr('data-src'),
                    type: 'get',
                    dataType: 'json',
                    data: '',
                })
                .done(function(response) {

                    if (response.result=='success') {
                        $('#POdetailsModel').find('#body').html(response.body);
                        $('#POdetailsModel').find('.modal-title').html(`Purchase Order Details`);
                        $('#POdetailsModel').modal('show');
                    }

                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
            });
        };
        showPODetails();

        const UploadPOAttachment = () => {
            $('.UploadPOAttachment').on('click', function () {

                let id = $(this).attr('data-id');

                $.ajax({
                    url: "<?php echo e(route('pms.billing-audit.po.list.attachment-upload')); ?>",
                    type: 'POST',
                    data: {_token: "<?php echo e(csrf_token()); ?>", id:id},
                })
                .done(function(response) {
                    $('#PurchaseOrderAttachmentModal').find('.modal-body').html(response);
                    $('#PurchaseOrderAttachmentModal').modal('show');

                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
            });
        };
        UploadPOAttachment();

        const isNumberKey =(evt) => {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
            {
                return false;
            }
            return true;
        };


        const viewRemarks = () => {
            $('.viewRemarks').on('click', function () {

                let model = $(this).attr('po-attachment');

                $('#POdetailsModel').find('#body').html(model);
                $('#POdetailsModel').find('.modal-title').html(`Notes From Audit`);
                $('#POdetailsModel').modal('show');
                
            });
        };
        viewRemarks();


    })(jQuery);

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/billing/po-list.blade.php ENDPATH**/ ?>