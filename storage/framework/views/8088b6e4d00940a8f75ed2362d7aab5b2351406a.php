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
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('requisition-acknowledge')): ?>
                        <th>Stock Qty</th>
                        <?php endif; ?>
                        <th>Requisition Qty</th>
                        <?php if($requisition->status==1): ?>
                        <th>Approved Qty</th>
                        <?php endif; ?>

                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $total_stock_qty = 0;
                    $total_requisition_qty = 0;
                    $total_approved_qty = 0;
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $requisition->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                    <tr>
                        <td><?php echo e($key+1); ?></td>

                        <td><?php echo e($item->product->category->name); ?></td>
                        <td><?php echo e($item->product->name); ?> (<?php echo e(getProductAttributes($item->product_id)); ?>)</td>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('requisition-acknowledge')): ?>
                        <td><?php echo e(isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0); ?></td>
                        <?php endif; ?>
                        <td><?php echo e(number_format($item->requisition_qty,0)); ?></td>
                        <?php if($requisition->status==1): ?>
                        <td><?php echo e($item->qty); ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('requisition-acknowledge')): ?>
                    <?php

                    $total_stock_qty += isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0;

                    ?>
                    <?php endif; ?>
                    <?php 
                    $total_requisition_qty += $item->requisition_qty;
                    $total_approved_qty += $item->qty;
                    ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                    <?php endif; ?>
                    <tr>
                        <td colspan="3" class="text-right">Total</td>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('requisition-acknowledge')): ?>
                        <td colspan=""><?php echo e($total_stock_qty); ?></td>
                        <?php endif; ?>
                        <td colspan=""><?php echo e($total_requisition_qty); ?></td>
                        <?php if($requisition->status==1): ?>
                        <td colspan=""><?php echo e($total_approved_qty); ?></td>
                        <?php endif; ?>

                    </tr>
                </tbody>
            </table>
            <div>
                <strong> Notes: </strong>
                <?php echo e($requisition->remarks); ?>

            </div>
            <?php if($requisition->status==2 && !empty($requisition->admin_remark)): ?>
            <div>
                <strong> Holding Reason: </strong>
                <?php echo $requisition->admin_remark; ?>

            </div>

            <?php endif; ?>

        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/requisitions/show.blade.php ENDPATH**/ ?>