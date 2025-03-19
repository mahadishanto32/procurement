
<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>

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
                <li class="active"><?php echo e(__($title)); ?></li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                   <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" border="0">
                    <thead>
                        <tr>
                            <th width="5%"><?php echo e(__('SL No.')); ?></th>
                            <th><?php echo e(__('Approved Date')); ?></th>
                            <th><?php echo e(__('Reference No')); ?></th>
                            <th><?php echo e(__('Supplier')); ?></th>

                            <th><?php echo e(__('Quotation Ref No')); ?></th>
                            <th><?php echo e(__('Total Price')); ?></th>
                            <th><?php echo e(__('Discount')); ?></th>
                            <th><?php echo e(__('Vat')); ?></th>
                            <th><?php echo e(__('Gross Price')); ?></th>

                            <th class="text-center"><?php echo e(__('Option')); ?></th>
                        </tr>
                    </thead>
                    <tbody id="viewResult">
                        <?php if(count($purchaseOrderList)>0): ?>
                        <?php $__currentLoopData = $purchaseOrderList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(($purchaseOrderList->currentpage()-1) * $purchaseOrderList->perpage() + $key + 1); ?></td>
                            <td><?php echo e(date('d-m-Y',strtotime($values->po_date))); ?></td>
                            <td> <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="<?php echo e(route('pms.purchase.order-list.show',$values->id)); ?>"><?php echo e($values->reference_no); ?></a></td>

                            <td><?php echo e($values->relQuotation?$values->relQuotation->relSuppliers->name:''); ?></td>

                            <td><?php echo e($values->relQuotation?$values->relQuotation->reference_no:''); ?></td>
                            <td><?php echo e($values->total_price); ?></td>
                            <td><?php echo e($values->discount); ?></td>
                            <td><?php echo e($values->vat); ?></td>
                            <td><?php echo e($values->gross_price); ?></td>


                            <td class="text-center">
                                <?php if($values->is_send=='no'): ?>
                                <a href="<?php echo e(route('pms.purchase.send-mail',$values->id)); ?>" class="btn btn-sm btn-primary"><i class="las la-paper-plane"></i>
                                    <?php echo e(__('Mail Send')); ?>

                                </a>
                                <?php else: ?>
                                <?php echo e(__('Already Sent')); ?>

                                <?php endif; ?>
                                <a target="__blank" href="<?php echo e(route('pms.billing-audit.po.invoice.print',$values->id)); ?>" class="btn btn-sm btn-warning"><i class="las la-print"></i>
                                    <?php echo e(__('Print View')); ?>

                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <?php if(count($purchaseOrderList)>0): ?>
                                <ul>
                                    <?php echo e($purchaseOrderList->links()); ?>

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/purchase/order-list.blade.php ENDPATH**/ ?>