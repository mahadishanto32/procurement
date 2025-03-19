
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
						<form action="<?php echo e(route('pms.po.cash.approval.list')); ?>" method="get" accept-charset="utf-8">
							<div class="row">
								
								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="cash_status"><?php echo e(__('Supplier')); ?>:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<select name="cash_status" id="cash_status" class="form-control rounded">
											<?php if(stringStatusArray()): ?>
											<?php $__currentLoopData = stringStatusArray(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<option value="<?php echo e($key); ?>" <?php echo e($cash_status == $key ? 'selected' : ''); ?>><?php echo e($status); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>

										</select>
									</div>
								</div>

								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label></label></p>
									<div class="input-group input-group-md">
										<button class="btn btn-success rounded mt-8" type="submit"> <i class="las la-search"></i>Search</button>
									</div>
								</div>
							</div>
						</form>
						
						<div class="page-content">
							<div class="">
								<div class="panel panel-info">
									<table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" border="0">
										<thead>
											<tr>
												<th width="5%"><?php echo e(__('SL No.')); ?></th>
												<th><?php echo e(__('Approved Date')); ?></th>
												<th><?php echo e(__('Reference No')); ?></th>
												<th><?php echo e(__('Job Type')); ?></th>

												<th><?php echo e(__('Quotation Ref No')); ?></th>
												<th><?php echo e(__('Total Price')); ?></th>
												<th><?php echo e(__('Discount')); ?></th>
												<th><?php echo e(__('Vat')); ?></th>
												<th><?php echo e(__('Gross Price')); ?></th>
												<th><?php echo e(__('Status')); ?></th>
												<th class="text-center"><?php echo e(__('Option')); ?></th>
											</tr>
										</thead>
										<tbody id="viewResult">
											<?php if(count($purchaseOrder)>0): ?>
											<?php $__currentLoopData = $purchaseOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<tr>
												<td><?php echo e(($purchaseOrder->currentpage()-1) * $purchaseOrder->perpage() + $key + 1); ?></td>
												<td><?php echo e(date('d-m-Y',strtotime($values->po_date))); ?></td>
												<td>
													<a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="<?php echo e(route('pms.purchase.order-list.show',$values->id)); ?>"><?php echo e($values->reference_no); ?></a></td>

													<td><?php echo e($values->relQuotation?$values->relQuotation->relSuppliers->name:''); ?></td>

													<td><?php echo e($values->relQuotation?$values->relQuotation->reference_no:''); ?></td>
													<td><?php echo e($values->total_price); ?></td>
													<td><?php echo e($values->discount); ?></td>
													<td><?php echo e($values->vat); ?></td>
													<td><?php echo e($values->gross_price); ?></td>
													<td>
														<?php if($values->cash_status=='approved'): ?>
														<button  class="btn btn-sm btn-primary"><i class="las la-clipboard-check"></i>
															<?php echo e(__('Approved')); ?>

														</button>
														<?php elseif($values->cash_status=='pending'): ?>
														<button  class="btn btn-sm btn-warning"><i class="las la-clipboard-check"></i>
															<?php echo e(__('Pending')); ?>

														</button>
														<?php else: ?>
														<button  class="btn btn-sm btn-danger"><i class="las la-clipboard-check"></i>
															<?php echo e(__('Halt')); ?>

														</button>
														<?php endif; ?>
														<a target="__blank" href="<?php echo e(route('pms.billing-audit.po.invoice.print',$values->id)); ?>" class="btn btn-sm btn-warning"><i class="las la-print"></i>
														</a>
													</td>
													<td class="text-center action">
														<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('po-cash-permission')): ?>
														<?php if($values->cash_status!='approved'): ?>
														<div class="btn-group">
															<button class="btn dropdown-toggle" data-toggle="dropdown">
																<span id="statusName<?php echo e($values->id); ?>">
																	<?php echo e(ucfirst($values->cash_status)); ?>

																</span>
															</button>
															<ul class="dropdown-menu">
																
																<?php if(stringStatusArray()): ?>
																<?php $__currentLoopData = stringStatusArray(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<li><a href="javascript:void(0)" class="updateCashStatus" data-id="<?php echo e($values->id); ?>" data-status="<?php echo e($key); ?>" title="Click Here To <?php echo e($status); ?>"> <?php echo e($status); ?></a>
																</li>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																<?php endif; ?>
															</ul>
														</div>
														<?php endif; ?>
														<?php endif; ?>
													</td>
												</tr>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												<?php endif; ?>
											</tbody>
										</table>

										<div class="row">
											<div class="col-md-12">
												<div class="pull-right">
													<?php if(count($purchaseOrder)>0): ?>
													<ul>
														<?php echo e($purchaseOrder->links()); ?>

													</ul>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
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

	<div class="modal" id="updateCashModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">Cash Approval</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<!-- Modal body -->
				<form action="<?php echo e(route('pms.po.cash.approval.store')); ?>" method="POST">
					<?php echo csrf_field(); ?>
					<div class="modal-body">
						<div class="form-group">
							<label for="cash_note">Notes (Optional):</label>

							<textarea class="form-control" name="cash_note" rows="3" id="cash_note" placeholder="Write here..."></textarea>
							<input type="hidden" readonly required name="id" id="purchase_order_id">
							<input type="hidden" readonly required name="cash_status" id="po_cash_status">
						</div>
					</div>
					<!-- Modal footer -->
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<?php $__env->stopSection(); ?>
	<?php $__env->startSection('page-script'); ?>
	<script>
		(function ($) {
			"use script";

			const updateCashStatus = () => {
				$('.updateCashStatus').on('click', function () {
					let id = $(this).attr('data-id');
					let status = $(this).attr('data-status');
					if (status){
						$('#purchase_order_id').val(id);
						$('#po_cash_status').val(status);
						return $('#updateCashModal').modal('show');
					}
				});
			};
			updateCashStatus();
		})(jQuery);
	</script>
	<?php $__env->stopSection(); ?>

<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/cash-approval/po-cash-approval.blade.php ENDPATH**/ ?>