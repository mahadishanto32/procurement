 <?php
 use App\Models\PmsModels\RequisitionDeliveryItem;
 ?>

 <div class="col-md-12">
    <div class="panel panel-info">
        <div class="col-lg-12 invoiceBody">
            <div class="invoice-details mt25 row">
                <div class="well col-12">
                    <ul class="list-unstyled mb0">
                        <li><strong><?php echo e(__('Product Name')); ?> :</strong> <?php echo e($product->name); ?></li>
                        <li><strong><?php echo e(__('Total Requistion')); ?> :</strong> <?php echo e($product->requisitionItem()->where('is_send','no')->whereIn('requisition_id',$requisitionIds)->count()); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="table-responsive style-scroll">
            <table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
                <thead>
                    <tr>
                        <th width="5%"><?php echo e(__('SL No.')); ?></th>
                        <th><?php echo e(__('REF No')); ?></th>
                        <th><?php echo e(__('Requisition By')); ?></th>
                        <th><?php echo e(__('Date')); ?></th>
                        <th class="text-center"><?php echo e(__('Items')); ?></th>
                        <th class="text-center"><?php echo e(__('Requisition Qty')); ?></th>
                        <th class="text-center"><?php echo e(__('Approved Qty')); ?></th>
                        <th class="text-center"><?php echo e(__('RFP Qty')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($items[0])): ?>
                    <?php 
                    $totalSumOfSendRFP= 0;
                    ?>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $sumOfSendRFP=RequisitionDeliveryItem::where('product_id',$item->product_id)->whereHas('relRequisitionDelivery.relRequisition', function($query){
                        return $query->where('status', 1)->where('request_status','send_rfp')
                        ->whereHas('requisitionItems', function($query2){
                            $query2->where('is_send','no');
                        });
                    })->sum('delivery_qty');

                    $totalSumOfSendRFP +=$item->qty-$sumOfSendRFP;
                    ?>
                    <tr>
                        <td>
                            <?php echo e($key+1); ?>

                        </td>
                        <td><?php echo e($item->requisition->reference_no); ?></td>
                        <td><?php echo e($item->requisition->relUsersList->name); ?></td>
                        <td>
                            <?php echo e(date("Y-m-d", strtotime($item->requisition->requisition_date))); ?>

                        </td>
                        <td><?php echo e($product->name); ?> (<?php echo e(getProductAttributes($product->id)); ?>)</td>
                        <td><?php echo e(number_format($item->requisition_qty,0)); ?></td>
                        <td><?php echo e($item->qty); ?></td>
                        <td><?php echo e(($sumOfSendRFP >0)?$item->qty-$sumOfSendRFP:$item->qty); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td colspan="5" class="text-right"><strong>Total</strong></td>
                        <td><?php echo e($items->sum('requisition_qty')); ?></td>
                        <td><?php echo e($items->sum('qty')); ?></td>
                        <td><?php echo e(($sumOfSendRFP >0)?$totalSumOfSendRFP:$items->sum('qty')); ?></td>
                    </tr>

                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/pages/proposal/_product-wise-requisition.blade.php ENDPATH**/ ?>