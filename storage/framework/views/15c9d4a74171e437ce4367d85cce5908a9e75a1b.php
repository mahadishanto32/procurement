<?php if(isset($purchaseOrder)): ?>

<div class="row">

    <?php 
    $TS = number_format($purchaseOrder->relQuotation->relSuppliers->SupplierRatings->sum('total_score'),2);
    $TC = $purchaseOrder->relQuotation->relSuppliers->SupplierRatings->count();

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
                                <a href="<?php echo e(route('pms.supplier.profile',$purchaseOrder->relQuotation->relSuppliers->id)); ?>" target="_blank"><span>Rating:</span></a> <?php echo ratingGenerate($totalScore,$totalCount); ?>

                            </div>
                            <h5 class="review-count"></h5>
                        </li>
                        <li><strong><?php echo e(__('Supplier')); ?> :</strong> <?php echo e($purchaseOrder->relQuotation->relSuppliers->name); ?></li>
                        <li><strong><?php echo e(__('Email')); ?> :</strong> <?php echo e($purchaseOrder->relQuotation->relSuppliers->email); ?></li>
                        <li><strong><?php echo e(__('Phone')); ?> :</strong> <?php echo e($purchaseOrder->relQuotation->relSuppliers->phone); ?></li>

                    </ul>
                </div>
                <div class="col-6">
                    <ul class="list-unstyled mb0 pull-right">
                        <li><strong><?php echo e(__('Date')); ?> :</strong> <?php echo e(date('d-m-Y',strtotime($purchaseOrder->po_date))); ?></li>
                        <li><strong><?php echo e(__('Reference No')); ?>:</strong> <?php echo e($purchaseOrder->reference_no); ?></li>
                        <li><strong><?php echo e(__('Quotation No')); ?>:</strong> <?php echo e($purchaseOrder->relQuotation->reference_no); ?></li>
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
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>


                    <?php $__currentLoopData = $purchaseOrder->relPurchaseOrderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($key+1); ?></td>
                        <td><?php echo e($item->relProduct->category->name); ?></td>
                        <td><?php echo e($item->relProduct->name); ?> (<?php echo e(getProductAttributes($item->product_id)); ?>)</td>
                        <td><?php echo e($item->relProduct->productUnit->unit_name); ?></td>
                        <td><?php echo e($item->unit_price); ?></td>
                        <td><?php echo e($item->qty); ?></td>
                        <td><?php echo e(number_format($item->sub_total_price,2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <tr>
                        <td colspan="4" class="text-right">Total</td>
                        <td colspan=""><?php echo e(number_format($purchaseOrder->relPurchaseOrderItems->sum('unit_price'),2)); ?></td>
                        <td colspan=""><?php echo e(number_format($purchaseOrder->relPurchaseOrderItems->sum('qty'),2)); ?></td>
                        <td colspan=""><?php echo e(number_format($purchaseOrder->relPurchaseOrderItems->sum('sub_total_price'),2)); ?></td>
                    </tr>

                    <tr>
                        <td colspan="6" class="text-right">(-) Discount</td>
                        <td><?= $purchaseOrder->discount?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right">(+) Vat</td>
                        <td><?php echo e($purchaseOrder->vat); ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right"><strong>Total Amount</strong></td>
                        <td><strong><?php echo e($purchaseOrder->gross_price); ?></strong></td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div class="form-group">
            <label for="remarks"><strong>Notes</strong>:</label>

            <span><?php echo $purchaseOrder->remarks?$purchaseOrder->remarks:''; ?></span>

        </div>
        <?php if($purchaseOrder->cash_note !=null): ?>
        <div class="form-group">
            <label for="remarks"><strong>Cash Notes</strong>:</label>

            <span><?php echo $purchaseOrder->cash_note?$purchaseOrder->cash_note:''; ?></span>

        </div>
        <?php endif; ?>

    </div>
</div>

</div>
<?php endif; ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/purchase/show.blade.php ENDPATH**/ ?>