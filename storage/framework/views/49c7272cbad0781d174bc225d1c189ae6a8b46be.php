<div class="col-md-12">
    <div class="panel panel-info">
        <div class="col-lg-12 invoiceBody">
            <div class="invoice-details mt25 row">
                <div class="well col-6">
                    <ul class="list-unstyled mb0">
                        <li><strong><?php echo e(__('Name')); ?> :</strong> <?php echo e(($requisition->relUsersList->name)?$requisition->relUsersList->name:''); ?></li>

                        <li><strong><?php echo e(__('Unit')); ?> :</strong> <?php echo e(($requisition->relUsersList->employee->unit->hr_unit_short_name)?$requisition->relUsersList->employee->unit->hr_unit_short_name:''); ?></li>
                        <li><strong><?php echo e(__('Department')); ?> :</strong> <?php echo e(($requisition->relUsersList->employee->department->hr_department_name)?$requisition->relUsersList->employee->department->hr_department_name:''); ?></li>
                    </ul>
                </div>
                <div class="col-6">
                    <ul class="list-unstyled mb0 pull-right">

                        <li><strong><?php echo e(__('Date')); ?> :</strong> <?php echo e(date('d-m-Y',strtotime($requisition->requisition_date))); ?></li>
                        <li><strong><?php echo e(__('Reference No')); ?>:</strong> <?php echo e($requisition->reference_no); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Sl No.</th>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Stock Qty</th>
                        <th>Requisition Qty</th>
                        <?php if($requisition->status==1): ?>
                        <th>Approved Qty</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($requisition)): ?>
                    <?php 
                    $totalStockQty = 0;
                    $totalRequisitionQty  = 0;
                    $totalApprovedQty = 0;
                    ?>
                    <?php $__currentLoopData = $requisition->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php 
                        $stockQty = collect($item->product->relInventoryDetails)->where('hr_unit_id',auth()->user()->employee->as_unit_id)->sum('qty');
                    ?>
                    <tr>
                        <td><?php echo e($key+1); ?></td>
                        <td><?php echo e($item->product->category->name); ?></td>
                        <td><?php echo e($item->product->name); ?> (<?php echo e(getProductAttributes($item->product->id)); ?>)</td>
                        <td><?php echo e($item->product->productUnit->unit_name); ?></td>
                        <td><?php echo e($stockQty); ?></td>
                        <td><?php echo e(number_format($item->requisition_qty,0)); ?></td>
                        <?php if($requisition->status==1): ?>
                        <td><?php echo e($item->qty); ?></td>
                        <?php endif; ?>
                    </tr>

                    <?php 

                    $totalStockQty += $stockQty;
                    $totalRequisitionQty += $item->requisition_qty;
                    $totalApprovedQty += $item->qty;
                    ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    <tr>
                        <td colspan="4" class="text-right">Total</td>
                        
                        <td colspan=""><?php echo e($totalStockQty); ?></td>
                        
                        <td colspan=""><?php echo e($totalRequisitionQty); ?></td>
                        
                        <td colspan=""><?php echo e($totalApprovedQty); ?></td>


                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/pages/store/store-inventory-compare.blade.php ENDPATH**/ ?>