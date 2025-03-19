
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
use App\Models\PmsModels\SupplierPayment;
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
                <li class="active">Accounts</li>
                <li class="active"><?php echo e(__($title)); ?></li>

            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="panel">
                <div class="table-responsive panel panel-body">
                    <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" cellspacing="0" width="100%" id="dataTable">
                        <thead>
                            <tr>
                               <th width="5%"><?php echo e(__('SL No.')); ?></th>
                               <th><?php echo e(__('P.O. Date')); ?></th>
                               <th><?php echo e(__('Supplier')); ?></th>
                               <th><?php echo e(__('Reference No')); ?></th>
                               <th><?php echo e(__('P.O Qty')); ?></th>
                               <th class="text-center"><?php echo e(__('Po Amount')); ?></th>
                               <th class="text-center"><?php echo e(__('GRN Qty')); ?></th>
                               <th class="text-center"><?php echo e(__('GRN Amount')); ?></th>
                               <th class="text-center"><?php echo e(__('Bill Amount')); ?></th>
                               <th class="text-center"><?php echo e(__('Paid Amount')); ?></th>
                               <th class="text-center"><?php echo e(__('Status')); ?></th>
                               <th class="text-center"><?php echo e(__('Invoice')); ?></th>
                               <th class="text-center"><?php echo e(__('Vat')); ?></th>
                               <th class="text-center"><?php echo e(__('Option')); ?></th>
                           </tr>
                       </thead>
                       <tbody id="viewResult">
                        <?php if(count($purchase_order)>0): ?>
                        <?php $__currentLoopData = $purchase_order; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $po_attachment=PurchaseOrderAttachment::where('purchase_order_id',$values->id)->where('bill_type','po')->first();
                        ?>
                        <tr>
                            <td><?php echo e($key + 1); ?></td>
                            <td><?php echo e(date('d-m-Y',strtotime($values->po_date))); ?></td>
                            <td><?php echo e($values->relQuotation?$values->relQuotation->relSuppliers->name:''); ?></td>
                            <td> <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="<?php echo e(route('pms.purchase.order-list.show',$values->id)); ?>"><?php echo e($values->reference_no); ?></a></td>

                            <td><?php echo e($values->relPurchaseOrderItems->sum('qty')); ?></td>
                            <td class="text-center"><?php echo e(number_format($values->gross_price,2)); ?></td>
                            <td class="text-center"><?php echo e($values->total_grn_qty); ?></td>
                            <td class="text-center">
                                <?php if($values->relGoodsReceivedItemStockIn): ?>

                                <?php echo e(number_format($values->relGoodsReceivedItemStockIn()->where('is_grn_complete','yes')->sum('total_amount'),2)); ?>

                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo e(PurchaseOrderAttachment::where('purchase_order_id',$values->id)->sum('bill_amount') > 0 ? number_format(PurchaseOrderAttachment::where('purchase_order_id',$values->id)->sum('bill_amount'), 2)  : 'Not Updated Yet'); ?></td>
                            <td class="text-center"><?php echo e(number_format(SupplierPayment::where('purchase_order_id',$values->id)->sum('pay_amount'), 2)); ?></td>

                            <td class="text-center text-left">
                                <?php if($values->relPurchaseOrderItems->sum('qty')==$values->total_grn_qty??0): ?>
                                <?php echo e(__('Full Received')); ?>

                                <?php else: ?>
                                <?php echo e(__('Partial Received')); ?>

                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if(isset($po_attachment)): ?>
                                <a href="<?php echo e(asset($po_attachment->invoice_file)); ?>" target="__blank" class="btn btn-success btn-xs">
                                    <i class="las la-eye">Invoice</i>
                                </a>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if(isset($po_attachment)): ?>
                                <a href="<?php echo e(asset($po_attachment->vat_challan_file)); ?>" class="btn btn-success btn-xs" target="__blank">
                                    <i class="las la-eye">Vat</i>
                                </a>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center">
                                <a href="<?php echo e(route('pms.accounts.po.invoice.list',$values->id)); ?>" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Click here to PO Challan List">Challan(<?php echo e($values->relGoodReceiveNote->count()); ?>) </a>

                                <a target="__blank" href="<?php echo e(route('pms.billing-audit.po.invoice.print',$values->id)); ?>" class="btn btn-warning btn-xs"><i class="las la-print"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-12">
                        <div class="la-1x pull-right">
                            <?php if(count($purchase_order)>0): ?>
                            <ul>
                                <?php echo e($purchase_order->links()); ?>

                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/accounts/billing-list.blade.php ENDPATH**/ ?>