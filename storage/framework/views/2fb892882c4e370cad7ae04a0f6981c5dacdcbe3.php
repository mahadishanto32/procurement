
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
					<a href="javascript:history.back()" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
				</li>
			</ul>
		</div>

		<div class="page-content">
			<div class="">
				<div class="panel panel-info">
					<div class="panel-body">
							
						<?php echo Form::open(['route' => 'pms.grn.stock.in.store',  'files'=> false, 'id'=>'', 'class' => '']); ?>

						<div class="table-responsive style-scroll">
							<table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="5%"><?php echo e(__('SL No.')); ?></th>
										<th><?php echo e(__('Ref No')); ?></th>
										<th><?php echo e(__('Category')); ?></th>
										<th><?php echo e(__('Product')); ?></th>
										<th><?php echo e(__('Unit Price')); ?></th>
										<th width="10%"><?php echo e(__('Qty')); ?></th>
										<th><?php echo e(__('Total Price')); ?></th>
										<th><?php echo e(__('Is Stock In')); ?></th>
										<th><?php echo e(__('Warehouses')); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php if(count($grn_stock_in_lists)>0): ?>
									<?php $__currentLoopData = $grn_stock_in_lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr id="hideRow<?php echo e($values->id); ?>">
										<td> <?php echo e($key + 1); ?></td>
										<?php if($key==0): ?>
										<td rowspan="<?php echo e($grn_stock_in_lists->count()); ?>"><?php echo e(ucfirst($values->relGoodsReceivedItems->relGoodsReceivedNote->reference_no)); ?></td>
										<?php endif; ?>
										<td><?php echo e($values->relGoodsReceivedItems->relProduct->category->name); ?></td>
										<td><?php echo e($values->relGoodsReceivedItems->relProduct->name); ?> (<?php echo e(getProductAttributes($values->relGoodsReceivedItems->product_id)); ?>)</td>
										
										<td><?php echo e(number_format($values->unit_amount,2)); ?></td>
										<td><input type="text" name="id[<?php echo e($values->id); ?>]" value="<?php echo e($values->received_qty); ?>" readonly class="form-control rounded"></td>
										<td><?php echo e(number_format($values->total_amount,2)); ?></td>
										
										<td><?php echo e(ucfirst($values->is_grn_complete)); ?></td>
										<td class="text-center">
											<div class="input-group input-group-md mb-3 d-">
												<select style="width:200px" name="warehouse_id[<?php echo e($values->id); ?>]" id="warehouse_id<?php echo e($values->id); ?>" class="form-control rounded" required>
													<option value="">Select One</option>
													<?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($warehouse->id); ?>" <?php echo e($warehouses->count() == 1 ? 'selected' : ''); ?>><?php echo e($warehouse->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
											</div>
										</td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endif; ?>
								</tbody>
								<input type="hidden" name="goods_received_note_id" value="<?php echo e($id); ?>">
								
							</table>
							<div class="col-12 py-2">
								<button type="submit" class="btn btn-success float-right">Submit</button>
							</div>
						</div>
						<?php echo Form::close(); ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="warehouseDetailModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">GRN Details</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<!-- Modal body -->
			<div class="modal-body" id="tableData">
			</div>
			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
<script>
	(function ($) {
		"use script";

		const grnStockInProcess = () => {
            $('.grnStockInProcess').on('click', function () {
                let id= $(this).attr('data-id');
                swal({
                	title: "<?php echo e(__('Are you sure?')); ?>",
                	text: 'Would you like to complete GRN process and Stock In?',
                	icon: "warning",
                	dangerMode: true,
                	buttons: {
                		cancel: true,
                		confirm: {
                			text: 'Approved',
                			value: true,
                			visible: true,
                			closeModal: true
                		},
                	},
                }).then((value) => {
                	$.ajax({
                		url: $(this).attr('data-src'),
                		type: 'post',
                		dataType: 'json',
                		data:{_token:'<?php echo csrf_token(); ?>',id:id},
                		success:function (data) {
                			if(data.result == 'success'){
                				$('#hideRow'+id).hide();
                				notify(data.message,'success');
                			}else{
                				notify(data.message,'error');
                			}
                		}
                	});

                });
            });
        };
        grnStockInProcess();

	})(jQuery);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/grn-stock-in/grn-stock-in-list.blade.php ENDPATH**/ ?>