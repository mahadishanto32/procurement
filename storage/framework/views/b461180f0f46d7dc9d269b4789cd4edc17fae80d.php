
<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
<style type="text/css">
    .list-unstyled .ratings {
        display: none;
    }
</style>
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
                <li class="top-nav-btn">
                    
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="panel panel-body">
                <div id="viewResult">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('SL No.')); ?></th>
                                    <th><?php echo e(__('PO Reference')); ?></th>
                                    <th><?php echo e(__('Gate-In Reference')); ?></th>
                                    <th><?php echo e(__('GRN Reference')); ?></th>
                                    <th><?php echo e(__('Gate-In Date')); ?></th>
                                    <th><?php echo e(__('Po Qty')); ?></th>
                                    <th><?php echo e(__('Gate-In Qty')); ?></th>
                                    <th><?php echo e(__('Approved Qty')); ?></th>
                                    <th><?php echo e(__('Return Qty')); ?></th>
                                    <th><?php echo e(__('Replace Qty')); ?></th>
                                    <th><?php echo e(__('Receive Status')); ?></th>
                                    <th><?php echo e(__('Slip')); ?></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if(count($purchaseOrder)>0): ?>
                                <?php $__currentLoopData = $purchaseOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkey=> $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($po->relGoodReceiveNote->count() > 0): ?>
                                    <?php $__currentLoopData = $po->relGoodReceiveNote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rkey => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <?php if($rkey == 0): ?>
                                        <td rowspan="<?php echo e($po->relGoodReceiveNote->count()); ?>"><?php echo e(($purchaseOrder->currentpage()-1) * $purchaseOrder->perpage() + $pkey + 1); ?></td>
                                        <td rowspan="<?php echo e($po->relGoodReceiveNote->count()); ?>">
                                            <a href="javascript:void(0)" class="btn btn-link showGRNPODetails" data-src="<?php echo e(route('pms.purchase.order-list.show',$values->relPurchaseOrder->id)); ?>" data-title="Purchase Order Details"><?php echo e($values->relPurchaseOrder->reference_no); ?>

                                            </a>
                                        </td>
                                        <?php endif; ?>
                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-link showGRNPODetails" data-src="<?php echo e(route('pms.grn.grn-process.show',$values->id)); ?>" data-title="Gate-In Details"><?php echo e($values->reference_no); ?>

                                            </a>
                                        </td>
                                        <td>
                                            
                                            <?php echo e($values->grn_reference_no); ?>

                                        </td>
                                        <td>
                                            <?php echo e(date('d-M-Y',strtotime($values->received_date))); ?>

                                        </td>
                                        <td><?php echo e($values->relPurchaseOrder->relPurchaseOrderItems->sum('qty')); ?></td>
                                        <td><?php echo e($values->relGoodsReceivedItems->sum('qty')); ?></td>
                                        <td>
                                            <?php echo e($values->relGoodsReceivedItems->where('quality_ensure','approved')->sum('received_qty')); ?>

                                        </td>
                                        <td>
                                            <?php echo e($values->relGoodsReceivedItems->where('quality_ensure','return')->sum('qty')-$values->relGoodsReceivedItems->where('quality_ensure','return')->sum('received_qty')); ?>

                                        </td>
                                        <td>
                                            <?php echo e($values->relGoodsReceivedItems->where('quality_ensure','return-change')->sum('qty')-$values->relGoodsReceivedItems->where('quality_ensure','return-change')->sum('received_qty')); ?>

                                        </td>
                                        <td class="capitalize"><?php echo e(ucfirst($values->received_status)); ?> Received</td>
                                        <td class="text-center">
                                            <a class="btn btn-success btn-xs" href="<?php echo e(url('pms/grn-slip/'.$values->id)); ?>" target="_blank"><i class="la la-print"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="col-12 py-2">
                            <?php if(count($purchaseOrder)>0): ?>
                                <ul>
                                    <?php echo e($purchaseOrder->links()); ?>

                                </ul>
                            <?php endif; ?>
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
<?php $__env->startSection('page-script'); ?>
<script>

   (function ($) {
    "use script";
    const showGRNPODetails = () => {
        $('.showGRNPODetails').on('click', function () {

            var modalTitle= $(this).attr('data-title');
            $.ajax({
                url: $(this).attr('data-src'),
                type: 'get',
                dataType: 'json',
                data: '',
            })
            .done(function(response) {

                if (response.result=='success') {
                    $('#POdetailsModel').find('#body').html(response.body);
                    $('#POdetailsModel').find('.modal-title').html(modalTitle);
                    $('#POdetailsModel').modal('show');
                }
            })
            .fail(function(response){
                notify('Something went wrong!','error');
            });
        });
    }
    showGRNPODetails();

})(jQuery);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/grn-stock-in/grn-list.blade.php ENDPATH**/ ?>