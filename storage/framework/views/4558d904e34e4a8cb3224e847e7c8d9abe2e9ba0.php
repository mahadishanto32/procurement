<div class="col-md-12">
    <div class="panel panel-info">
        <div class="col-lg-12 invoiceBody">
            <div class="invoice-details mt25 row">
                <div class="well col-6">
                    <strong>Assign to suppliers:</strong>
                    <ul class="list-unstyled mb0">

                        <li><strong><?php echo e(__('Name')); ?> :</strong>
                        <?php echo e($requestProposal->defineToSupplier->pluck('supplier.name')->implode(', ')); ?>

                        </li>
                    </ul>
                </div>
                <div class="col-6">
                    <ul class="list-unstyled mb0 pull-right">

                        <li><strong><?php echo e(__('Date')); ?> :</strong> <?php echo e(date('d-m-Y',strtotime($requestProposal->request_date))); ?></li>
                        <li><strong><?php echo e(__('Reference No')); ?>:</strong> <?php echo e($requestProposal->reference_no); ?></li>
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
                        <th>Qty</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if($requestProposal->requestProposalDetails): ?>
                    <?php
                    $requestQty=0;
                    ?>
                    <?php $__currentLoopData = $requestProposal->requestProposalDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$requestProposalDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($key+1); ?></td>
                        <td><?php echo e($requestProposalDetail->product->category->name); ?></td>
                        <td><?php echo e($requestProposalDetail->product->name); ?> (<?php echo e(getProductAttributes($requestProposalDetail->product_id)); ?>)</td>
                        <td><?php echo e($requestProposalDetail->request_qty); ?></td>
                        
                    </tr>
                    <?php
                    $requestQty+=$requestProposalDetail->request_qty;
                    ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <tr>
                        <td colspan="3">Total</td>
                        <td><?php echo e($requestQty); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/rfp/request-proposal-details.blade.php ENDPATH**/ ?>