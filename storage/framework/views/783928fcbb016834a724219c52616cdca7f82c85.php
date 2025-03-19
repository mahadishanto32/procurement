
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
                    <a href="<?php echo e(route('pms.quality.ensure.approved.list')); ?>" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">

                <div class="panel-body">
                    <div class="table-responsive">

                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Category</th>
                                    <th>Product</th>
                                    <th>Unit</th>
                                    <th>Unit Price</th>
                                    <th>Qty</th>
                                    <th>Received Qty</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sumOfReceivedtQty=0;
                                
                                $sumOfItemQty=0;
                                $sumOfSubtotal=0;

                                $discountAmount= 0;
                                $vatAmount= 0;

                                ?>
                                <?php if(isset($approval_list)): ?>
                                <?php $__currentLoopData = $approval_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php 
                                $sumOfReceivedtQty +=($item->received_qty);
                                $sumOfItemQty +=($item->relGoodsReceivedItems->qty);
                                
                                $sumOfSubtotal += $item->unit_amount*$item->received_qty;

                                $discountAmount +=($item->discount_percentage * $item->unit_amount*$item->received_qty)/100;

                                $vatAmount +=($item->vat_percentage * $item->unit_amount*$item->received_qty)/100;
                                ?>

                                <tr id="removeApprovedRow<?php echo e($item->id); ?>">
                                    <td><?php echo e($key+1); ?></td>
                                    <td><?php echo e($item->relGoodsReceivedItems->relProduct->category->name); ?></td>
                                    <td><?php echo e($item->relGoodsReceivedItems->relProduct->name); ?> (<?php echo e(getProductAttributes($item->relGoodsReceivedItems->relProduct->id)); ?>)</td>
                                    <td><?php echo e($item->relGoodsReceivedItems->relProduct->productUnit->unit_name); ?></td>
                                    <td><?php echo e($item->unit_amount); ?></td>
                                    <td><?php echo e(number_format($item->relGoodsReceivedItems->qty,0)); ?></td>
                                    <td><?php echo e($item->received_qty); ?></td>
                                    <td><?php echo e(number_format($item->unit_amount*$item->received_qty,2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td colspan="4" class="text-right">Total</td>
                                    <td colspan=""><?php echo e(isset($approval_list)?number_format($approval_list->sum('unit_amount'),2):0); ?></td>
                                    <td colspan=""><?php echo e(isset($sumOfItemQty)?number_format($sumOfItemQty,0):0); ?></td>
                                    <td><?php echo e(isset($sumOfReceivedtQty)?number_format($sumOfReceivedtQty,0):0); ?></td>
                                    
                                    <td colspan=""><?php echo e(isset($approval_list)?number_format($sumOfSubtotal,2):0); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right">(-) Discount</td>
                                    <td><?= number_format($discountAmount,2)?></td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right">(+) Vat</td>
                                    <td><?php echo e(number_format($vatAmount,2)); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right"><strong>Total Amount</strong></td>
                                    <td><strong><?php echo e(number_format(($sumOfSubtotal-$discountAmount)+$vatAmount,2)); ?></strong></td>
                                </tr>
                                <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-right">No Data Found</td>
                                </tr>
                                <?php endif; ?>

                            </tbody>
                        </table>
            </div>
            
        </div>

    </div>
</div>
</div>
</div>



<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
<script>

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/quality/approved-list.blade.php ENDPATH**/ ?>