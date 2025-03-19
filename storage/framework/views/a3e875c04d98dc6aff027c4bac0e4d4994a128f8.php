<div class="col-md-12">
    <div class="form-group">
        <div class="table-responsive ">
            <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                <thead>
                    <tr>
                        <th><?php echo e(__('GRN Reference')); ?></th>
                        <th><?php echo e(__('GRN Date')); ?></th>
                        <th><?php echo e(__('Challan No')); ?></th>
                        <th><?php echo e(__('Po Qty')); ?></th>
                        <th><?php echo e(__('GRN Qty')); ?></th>
                        <th><?php echo e(__('GRN Amount')); ?></th>
                        <th class="text-center"><?php echo e(__('Bill Amount')); ?></th>
                        <th><?php echo e(__('Receive Status')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $bill_amount = 0;
                    ?>
                    <?php if($po->relGoodReceiveNote->count() > 0): ?>
                    <?php $__currentLoopData = $po->relGoodReceiveNote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$grn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php 
                    $po_attachment=\App\Models\PmsModels\Purchase\PurchaseOrderAttachment::where('purchase_order_id',$po->id)
                    ->where('goods_received_note_id',$grn->id)
                    ->where('bill_type','grn')
                    ->first();
                    ?>
                    <?php if(!isset($po_attachment->id)): ?>
                    <?php 
                    $goodsReceiveItemsId=\App\Models\PmsModels\Grn\GoodsReceivedItem::where('goods_received_note_id',$grn->id)
                    ->pluck('id')
                    ->all();
                    $grn_amount = $po->relGoodsReceivedItemStockIn()
                    ->whereIn('goods_received_item_id',$goodsReceiveItemsId)
                    ->where('is_grn_complete','yes')
                    ->sum('total_amount');
                    $bill_amount = $bill_amount+$grn_amount;
                    ?>
                    <tr>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="<?php echo e(route('pms.grn.grn-process.show',$grn->id)); ?>" data-title="GRN Details"><?php echo e($grn->reference_no); ?>

                            </a>
                        </td>
                        <td>
                            <?php echo e(date('d-M-Y',strtotime($grn->received_date))); ?>

                        </td>
                        <td>
                            <?php echo e($grn->challan); ?>

                        </td>
                        <td><?php echo e($po->relPurchaseOrderItems->sum('qty')); ?></td>
                        <td><?php echo e($grn->relGoodsReceivedItems->sum('qty')); ?></td>

                        <td class="text-right">
                            <?php if($goodsReceiveItemsId): ?>
                            <?php echo e($grn_amount); ?>

                            <?php endif; ?>
                        </td>
                        <td class="text-center">Not Updated Yet</td>
                        <td class="capitalize"><?php echo e($grn->received_status); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <tr>
                        <td colspan="5" class="text-right"><strong>Total GRN Amount:</strong></td>
                        <td class="text-right"><?php echo e($bill_amount); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="bill_number"><strong>Bill Number</strong></label>
                <input type="text" name="bill_number" value="<?php echo e(isset($po_bill->bill_number) ? $po_bill->bill_number : ''); ?>" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="bill_amount"><strong>Bill Amount</strong></label>
                <input type="text" name="bill_amount" value="<?php echo e(isset($po_bill->bill_amount) ? $po_bill->bill_amount : $bill_amount); ?>" class="form-control" readonly>
            </div>
        </div>
    </div>

</div>
<div class="col-md-12">
    <div class="form-group">
        <div class="form-line">
            <?php echo Form::label('invoice_file', 'Invoice File (Supported format ::jpeg,jpg,png,gif,pdf & file size max :: 5MB)', array('class' => 'col-form-label')); ?>


            <div style="position:relative;">
                <a class='btn btn-primary btn-xs font-10' href='javascript:;'>
                    Choose File...
                    <input name="invoice_file" type="file" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' size="40" onchange='$("#upload-file-info").html($(this).val());'>
                </a>
                &nbsp;
                <span class='label label-info' id="upload-file-info"></span>
            </div>

        </div>
    </div> 
</div>

<div class="col-md-12">
    <div class="form-group">
        <div class="form-line">

            <?php echo Form::label('vat_challan_file', 'Vat Challan No (Supported format ::jpeg,jpg,png,gif,pdf & file size max :: 5MB)', array('class' => 'col-form-label')); ?>

            <div style="position:relative;">
                <a class='btn btn-success btn-xs font-10' href='javascript:;'>
                    Choose File...
                    <input name="vat_challan_file" type="file" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' size="40" onchange='$("#upload-vat-file-info").html($(this).val());'>
                </a>
                &nbsp;
                <span class='label label-success' id="upload-vat-file-info"></span>
            </div>
        </div>
    </div> 
</div>

<input type="hidden" readonly required name="purchase_order_id" id="purchase_order_id" value="<?php echo e($po->id); ?>"><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/billing/_po-attachement-upload.blade.php ENDPATH**/ ?>