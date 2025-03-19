
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
            <div class="">

                <div class="panel-body">
                    <div class="table-responsive ">
                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('SL No.')); ?></th>
                                    <th><?php echo e(__('P.O Reference')); ?></th>
                                    <th><?php echo e(__('P.O Date')); ?></th>
                                    <th><?php echo e(__('Challan No')); ?></th>
                                    <th><?php echo e(__('Gate-In Reference')); ?></th>
                                    <th><?php echo e(__('Gate-In Date')); ?></th>
                                    <th><?php echo e(__('Po Qty')); ?></th>
                                    <th><?php echo e(__('Gate-In Qty')); ?></th>
                                    <th><?php echo e(__('Receive Status')); ?></th>
                                    <th><?php echo e(__('Approved Qty')); ?></th>
                                    <th><?php echo e(__('Option')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(isset($purchaseOrder[0])): ?>
                            <?php $__currentLoopData = $purchaseOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkey => $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $goodReceivedNotes = \App\Models\PmsModels\Grn\GoodsReceivedNote::where('purchase_order_id', $po->id)
                                ->whereHas('relGoodsReceivedItems', function($query){
                                    return $query->where('quality_ensure','approved');
                                })->get();
                            ?>  
                                <?php if($goodReceivedNotes->count() > 0): ?>
                                <?php $__currentLoopData = $goodReceivedNotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rkey => $grn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <?php if($rkey == 0): ?>
                                    <td rowspan="<?php echo e($goodReceivedNotes->count()); ?>"><?php echo e(($purchaseOrder->currentpage()-1) * $purchaseOrder->perpage() + $pkey + 1); ?></td>

                                    <td rowspan="<?php echo e($goodReceivedNotes->count()); ?>">
                                        <a href="javascript:void(0)" class="btn btn-link showQEPODetails" data-src="<?php echo e(route('pms.purchase.order-list.show',$grn->relPurchaseOrder->id)); ?>" data-title="Purchase Order Details"><?php echo e($grn->relPurchaseOrder->reference_no); ?>

                                        </a>
                                    </td>
                                    <td rowspan="<?php echo e($goodReceivedNotes->count()); ?>">
                                        <?php echo e(date('d-M-Y',strtotime($po->po_date))); ?>

                                    </td>
                                    <?php endif; ?>
                                    <td><?php echo e($grn->challan); ?></td>
                                    <td>
                                        <a href="javascript:void(0)" class="btn btn-link showQEPODetails" data-src="<?php echo e(route('pms.grn.grn-process.show',$grn->id)); ?>" data-title="Gate-In Details"><?php echo e($grn->reference_no); ?>

                                        </a>
                                        <a class="btn btn-primary btn-xs" href="<?php echo e(url('pms/grn/gate-in-slip/'.$po->id.'?grn='.$grn->id)); ?>" target="_blank"><i class="la la-print"></i></a>
                                    </td>

                                    <td>
                                        <?php echo e(date('d-M-Y',strtotime($grn->received_date))); ?>

                                    </td>


                                    <?php if($rkey == 0): ?>
                                    <td rowspan="<?php echo e($goodReceivedNotes->count()); ?>">
                                        <?php echo e($po->relPurchaseOrderItems->sum('qty')); ?>

                                    </td>
                                    <?php endif; ?>
                                    
                                    <td><?php echo e($grn->relGoodsReceivedItems->sum('qty')); ?></td>
                                    <td class="capitalize"><?php echo e($grn->received_status); ?></td>
                                    <td><?php echo e($grn->relGoodsReceivedItems->where('quality_ensure','approved')->sum('qty')); ?></td>
                                    <td>
                                        <?php $count= $grn->relGoodsReceivedItems()->where('quality_ensure','approved')->count(); ?>
                                        <?php if($count > 0): ?>
                                        <a href="<?php echo e(route('pms.quality.ensure.approved.single.list',$grn->id)); ?>" class="btn btn-xs btn-info"><?php echo e(__('Items')); ?> (<?php echo e($count); ?>)</a>
                                        <?php endif; ?>
                                        <a target="__blank" href="<?php echo e(route('pms.quality.approved.item.print',['id'=>$grn->id,'type'=>'approved'])); ?>" title="Approved List" class="btn btn-xs btn-success"><i class="las la-print"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>

                               
                            </tbody>
                        </table>
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

        const showQEPODetails = () => {
            $('.showQEPODetails').on('click', function () {

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
        showQEPODetails();

    })(jQuery);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/quality/approved-index.blade.php ENDPATH**/ ?>