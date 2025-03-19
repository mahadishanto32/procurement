<?php if(isset($grn)): ?>

<div class="row">

    <?php 
    $TS = number_format($grn->relPurchaseOrder->relQuotation->relSuppliers->SupplierRatings->sum('total_score'),2);
    $TC = $grn->relPurchaseOrder->relQuotation->relSuppliers->SupplierRatings->count();

    $totalScore = isset($TS)?$TS:0;
    $totalCount = isset($TC)?$TC:0;
?>

<div class="col-md-12">
    <div class="panel panel-info">

        <div class="col-lg-12 invoiceBody">
            <div class="invoice-details mt25 row">

                <div class="well col-6">
                    <ul class="list-unstyled mb0">
                        <li>
                            <div class="ratings">
                                <a href="<?php echo e(route('pms.supplier.profile',$grn->relPurchaseOrder->relQuotation->relSuppliers->id)); ?>" target="_blank"><span>Rating:</span></a> <?php echo ratingGenerate($totalScore,$totalCount); ?>

                            </div>
                            <h5 class="review-count"></h5>
                        </li>
                        <li><strong><?php echo e(__('Supplier')); ?> :</strong> <?php echo e($grn->relPurchaseOrder->relQuotation->relSuppliers->name); ?></li>
                        <li><strong><?php echo e(__('Email')); ?> :</strong> <?php echo e($grn->relPurchaseOrder->relQuotation->relSuppliers->email); ?></li>
                        <li><strong><?php echo e(__('Phone')); ?> :</strong> <?php echo e($grn->relPurchaseOrder->relQuotation->relSuppliers->phone); ?></li>
                        <li><strong><?php echo e(__('Address')); ?>:</strong> <?php echo e($grn->relPurchaseOrder->relQuotation->relSuppliers->address); ?></li>

                    </ul>
                </div>
                <div class="col-6">
                    <ul class="list-unstyled mb0 pull-right">
                        <li><strong><?php echo e(__('Date')); ?> :</strong> <?php echo e(date('d-m-Y',strtotime($grn->received_date))); ?></li>
                        <li><strong><?php echo e(__('Gate-In Ref: No')); ?>:</strong> <?php echo e($grn->reference_no); ?></li>
                        <li><strong><?php echo e(__('GRN Ref: No')); ?>:</strong> <?php echo e($grn->grn_reference_no); ?></li>
                        <li><strong><?php echo e(__('Challan No.')); ?>:</strong> <?php echo e($grn->challan); ?></li>
                        <li><strong><?php echo e(__('Receive Qty.')); ?>:</strong> <?php echo e($grn->relGoodsReceivedItems->sum('qty')); ?></li>
                        <li><strong><?php echo e(__('Receive Status.')); ?>:</strong> <span class="capitalize"> <?php echo e($grn->received_status); ?></span></li>
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
                        <?php if(!auth()->user()->hasRole('Gate Permission')): ?>
                        <th>Unit Price</th>
                        <?php endif; ?>
                        <th>Qty</th>
                        <?php if(!auth()->user()->hasRole('Gate Permission')): ?>
                        <th>Price</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>


                    <?php $__currentLoopData = $grn->relGoodsReceivedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($key+1); ?></td>
                        <td><?php echo e($item->relProduct->category->name); ?></td>
                        <td><?php echo e($item->relProduct->name); ?> (<?php echo e(getProductAttributes($item->product_id)); ?>)</td>
                        <td><?php echo e($item->relProduct->productUnit->unit_name); ?></td>
                        <?php if(!auth()->user()->hasRole('Gate Permission')): ?>
                        <td><?php echo e($item->unit_amount); ?></td>
                        <?php endif; ?>
                        <td><?php echo e(number_format($item->qty,0)); ?></td>
                        <?php if(!auth()->user()->hasRole('Gate Permission')): ?>
                        <td><?php echo e(number_format($item->sub_total,2)); ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <tr>
                        <td colspan="4" class="text-right">Total</td>
                        <?php if(!auth()->user()->hasRole('Gate Permission')): ?>
                        <td colspan=""><?php echo e(number_format($grn->relGoodsReceivedItems->sum('unit_amount'),2)); ?></td>
                        <?php endif; ?>
                        <td colspan=""><?php echo e(number_format($grn->relGoodsReceivedItems->sum('qty'),0)); ?></td>
                        <?php if(!auth()->user()->hasRole('Gate Permission')): ?>
                        <td colspan=""><?php echo e(number_format($grn->relGoodsReceivedItems->sum('sub_total'),2)); ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php if(!auth()->user()->hasRole('Gate Permission')): ?>
                    <tr>
                        <td colspan="6" class="text-right">(-) Discount</td>
                        <td><?= number_format($grn->discount,2)?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right">(+) Vat</td>
                        <td><?php echo e(number_format($grn->vat,2)); ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right"><strong>Total Amount</strong></td>
                        <td><strong><?php echo e(number_format($grn->gross_price,2)); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
        <div class="form-group">
            <label for="remarks"><strong>Notes</strong>:</label>

            <span><?php echo $grn->note?$grn->note:''; ?></span>

        </div>

    </div>
</div>

</div>
<?php endif; ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/grn/show.blade.php ENDPATH**/ ?>