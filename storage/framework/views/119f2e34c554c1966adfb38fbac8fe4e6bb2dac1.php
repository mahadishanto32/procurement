
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
				<li>
					<a href="#">Accounts</a>
				</li>
				<li class="active"><?php echo e(__($title)); ?></li>
				<li class="top-nav-btn">
				</li>
			</ul>
		</div>

		<div class="page-content">
			<div class="">
				<div class="panel panel-info">
					<div class="panel-body">
						<form action="<?php echo e(url('pms/accounts/supplier-payments')); ?>" method="get" accept-charset="utf-8">
							<div class="row">
								
								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="supplier_id"><?php echo e(__('Supplier')); ?>:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<select name="supplier_id" id="supplier_id" class="form-control rounded">
											<option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
											<?php if(isset($suppliers[0])): ?>
											<?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<option value="<?php echo e($supplier->id); ?>" <?php echo e($supplier_id == $supplier->id ? 'selected' : ''); ?>><?php echo e($supplier->name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										</select>
									</div>
								</div>

								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="searchRequisitonBtn"></label></p>
									<div class="input-group input-group-md">
										<button class="btn btn-success rounded mt-8" type="submit"> <i class="las la-search"></i>Search</button>
									</div>
								</div>
							</div>
						</form>
						<form action="<?php echo e(route('pms.accounts.supplier.payment.store')); ?>" method="post" accept-charset="utf-8">
						<?php echo csrf_field(); ?>
							<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="8%"><?php echo e(__('SL No.')); ?></th>
										<th><?php echo e(__('Supplier Name')); ?></th>
										<th><?php echo e(__('PO Ref No')); ?></th>
										<th><?php echo e(__('PO Amount')); ?></th>
										
										<th><?php echo e(__('GRN Amount')); ?></th>
										<th><?php echo e(__('Bill Amount')); ?></th>
										<th><?php echo e(__('Paid Amount')); ?></th>
										<th><?php echo e(__('Due Amount')); ?></th>
										<th><?php echo e(__('Pay Amount')); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php if(isset($purchase_order)): ?>
									<?php $__currentLoopData = $purchase_order; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pokey=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php if($values->relSupplierPayments->count() > 0): ?>
									<?php $__currentLoopData = $values->relSupplierPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkey => $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr id="removeRow<?php echo e($values->id); ?>">
											<?php if($pkey == 0): ?>
											<td rowspan="<?php echo e($values->relSupplierPayments->count()); ?>">
												<?php echo e($key + 1); ?>

											</td>
											
											<td rowspan="<?php echo e($values->relSupplierPayments->count()); ?>"><?php echo e(ucfirst($values->relQuotation->relSuppliers->name)); ?></td>
											<td rowspan="<?php echo e($values->relSupplierPayments->count()); ?>">
												<a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="<?php echo e(route('pms.purchase.order-list.show',$values->id)); ?>"><?php echo e($values->reference_no); ?></a>
											</td>
											<td rowspan="<?php echo e($values->relSupplierPayments->count()); ?>" class="text-right">
												<?php echo e(number_format($values->gross_price,2)); ?>

											</td>
											<?php endif; ?>

											<?php
												$grn_amount = \App\Models\PmsModels\Grn\GoodsReceivedNote::whereHas('billingChallan.relPurchaseOrderAttachment', function($query) use($values, $payment){
													return $query->where([
														'purchase_order_id' => $values->id,
														'goods_received_note_id' => $payment->goods_received_note_id,
														'bill_type' => $payment->bill_type,
													]);
												})->sum('gross_price');
											?>
											
											<td class="text-right" width="10%">
												<input type="text" class="form-control text-right rounded grn-amounts" onkeypress="return isNumberKey(event)"  value="<?php echo e(number_format($grn_amount,2)); ?>" readonly>
											</td>

											<td class="text-right" width="10%">
												<input type="text" class="form-control text-right rounded bill-amounts" onkeypress="return isNumberKey(event)"  value="<?php echo e(number_format($payment->bill_amount,2)); ?>" readonly>
											</td>
											
											<td class="text-right" width="10%">
												<input type="text" class="form-control text-right rounded paid-amounts" onkeypress="return isNumberKey(event)"  value="<?php echo e(number_format($payment->pay_amount,2)); ?>" readonly>
											</td>
											<td class="text-right" width="10%">
												<input type="text" class="form-control text-right rounded due-amounts" <?php if($payment->bill_amount-$payment->pay_amount < 0): ?> name="due_amount[<?php echo e($payment->id); ?>]" <?php endif; ?> onkeypress="return isNumberKey(event)"  value="<?php echo e(number_format(($payment->bill_amount-$payment->pay_amount > 0 ? $payment->bill_amount-$payment->pay_amount : 0),2)); ?>" readonly>
											</td>
											<td class="text-right" width="10%">
												<?php if($payment->bill_amount-$payment->pay_amount > 0): ?>
													<input type="number" step="0.01" min="0" max="<?php echo e($payment->bill_amount-$payment->pay_amount); ?>" class="form-control text-right pay-amounts rounded" name="pay_amount[<?php echo e($payment->id); ?>]"  onkeypress="return isNumberKey(event)" onkeyup="calculateTotal()" onchange="calculateTotal()" value="<?php echo e($payment->bill_amount-$payment->pay_amount); ?>">
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endif; ?>
									
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endif; ?>
									<tr>
										<td colspan="4" class="text-right"><strong>Total:</strong></td>
										<td class="text-right" id="grn-amounts"></td>
										<td class="text-right" id="bill-amounts"></td>
										<td class="text-right" id="paid-amounts"></td>
										<td class="text-right" id="due-amounts"></td>
										<td class="text-right" id="pay-amounts"></td>
									</tr>
									<tr>
										<td colspan="9" class="text-right">
											<button class="btn btn-success rounded mt-8 mb-2" type="submit"> <i class="las la-check"></i>&nbsp;Submit Payments</button>
										</td>
									</tr>
								</tbody>
							</table>
						</form>
						<div class="p-3">
							<?php if(count($purchase_order)>0): ?>
							<?php echo e($purchase_order->links()); ?>

							<?php endif; ?>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script>
	(function ($) {
		"use script";

		$('.page-link').click(function(event) {
			event.preventDefault();

			window.open($(this).attr('href')+"&supplier_id=<?php echo e($supplier_id); ?>", "_parent");
		});

		const isNumberKey =(evt) => {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
            {
                return false;
            }
            return true;
        };

	})(jQuery);
</script>

<script type="text/javascript">
	calculateTotal();
	function calculateTotal(){
    	var grn_amount = 0;
    	$.each($('.grn-amounts'), function(index, val) {
    		grn_amount += parseFloat($(this).val().replace(',',''));
    	});

    	var bill_amount = 0;
    	$.each($('.bill-amounts'), function(index, val) {
    		bill_amount += parseFloat($(this).val().replace(',',''));
    	});

    	var paid_amount = 0;
    	$.each($('.paid-amounts'), function(index, val) {
    		paid_amount += parseFloat($(this).val().replace(',',''));
    	});

    	var due_amount = 0;
    	$.each($('.due-amounts'), function(index, val) {
    		due_amount += parseFloat($(this).val().replace(',',''));
    	});

    	var pay_amount = 0;
    	$.each($('.pay-amounts'), function(index, val) {
    		pay_amount += parseFloat($(this).val().replace(',','') != "" ? $(this).val().replace(',','') : 0);
    	});

    	$('#grn-amounts').html('<strong>'+(grn_amount.toFixed(2))+'&nbsp;&nbsp;&nbsp;&nbsp;</strong>');
    	$('#bill-amounts').html('<strong>'+(bill_amount.toFixed(2))+'&nbsp;&nbsp;&nbsp;&nbsp;</strong>');
    	$('#paid-amounts').html('<strong>'+(paid_amount.toFixed(2))+'&nbsp;&nbsp;&nbsp;&nbsp;</strong>');
    	$('#due-amounts').html('<strong>'+(due_amount.toFixed(2))+'&nbsp;&nbsp;&nbsp;&nbsp;</strong>');
    	$('#pay-amounts').html('<strong>'+(pay_amount.toFixed(2))+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>');
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/accounts/supplier-payment-list.blade.php ENDPATH**/ ?>