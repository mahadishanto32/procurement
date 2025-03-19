<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
<?php
use App\Models\PmsModels\RequisitionItem;
?>
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
				</li>
			</ul><!-- /.breadcrumb -->
		</div>

		<div class="page-content">
			<div class="">
				<div class="panel panel-info">
					<div class="panel-body">
						
						<div class="row">
							<div class="col-md-3 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="from_date"><?php echo e(__('From Date')); ?>:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<input type="text" name="from_date" id="from_date" class="search-datepicker form-control rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="<?php echo e(old('from_date')?old('from_date'):date("d-m-Y", strtotime(date('Y-m-01')))); ?>">
								</div>
							</div>
							<div class="col-md-3 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="to_date"><?php echo e(__('To Date')); ?>:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<input type="text" name="to_date" id="to_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required readonly value="<?php echo e(old('to_date')?old('to_date'):date('d-m-Y')); ?>">
								</div>
							</div>

							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="department_id"><?php echo e(__('Department')); ?>:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<select name="department_id" id="department_id" class="form-control rounded">
										<option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
										<?php $__currentLoopData = $department; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($values->hr_department_id); ?>">
											<?php echo e($values->hr_department_name); ?>

										</option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
							</div>

							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="requisition_by"><?php echo e(__('Requisition Users')); ?>:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<select name="requisition_by" id="requisition_by" class="form-control rounded">
										<option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
										
									</select>
								</div>
							</div>

							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="rfpSearchStoreRequisitonBtn"></label></p>
								<div class="input-group input-group-md">
									<a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="<?php echo e(route('pms.store-manage.rfp.requisition.search')); ?>" id="rfpSearchStoreRequisitonBtn"> <i class="las la-search"></i>Search</a>
								</div>
							</div>
						</div>

						<div id="viewResult">
							<table class="table table-striped table-bordered table-head datatable-exportable" id="dataTable" data-table-name="<?php echo e($title); ?>" border="1">
								<thead>
									<tr>
										<th width="5%"><?php echo e(__('SL No.')); ?></th>
										<th><?php echo e(__('Unit')); ?></th>
										<th><?php echo e(__('Department')); ?></th>
										<th><?php echo e(__('Date')); ?></th>
										<th><?php echo e(__('Reference No')); ?></th>
										<th><?php echo e(__('Requisition By')); ?></th>
										<th><?php echo e(__('Stock Qty')); ?></th>
										<th><?php echo e(__('Req Qty')); ?></th>
										<th class="text-center"><?php echo e(__('Option')); ?></th>
									</tr>
								</thead>
								<tbody>
								<?php
									$totalStockQty = 0;
									$totalReqQty = 0;
									$sl = 0;
								?>
								<?php if(count($requisition)>0): ?>
								<?php $__currentLoopData = $requisition; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php 
									$stockQty=0;
									foreach($values->items as $item){
										$stockQty += collect($item->product->relInventoryDetails)->where('hr_unit_id',auth()->user()->employee->as_unit_id)->sum('qty');
									}

								?>
								<?php if($stockQty >0): ?>
								<?php
									$totalStockQty += $stockQty;
									$totalReqQty += $values->items->sum('qty');
									$sl++;
								?>
								<tr id="row<?php echo e($values->id); ?>">
									<td><?php echo e(($requisition->currentpage()-1) * $requisition->perpage() + $key + 1); ?></td>
									<td>
										<?php echo e($values->relUsersList->employee->unit->hr_unit_short_name?$values->relUsersList->employee->unit->hr_unit_short_name:''); ?>

									</td>
									<td>
										<?php echo e($values->relUsersList->employee->department->hr_department_name?$values->relUsersList->employee->department->hr_department_name:''); ?>

									</td>
									<td><?php echo e(date('d-m-Y', strtotime($values->requisition_date))); ?></td>
									
									<td><a href="javascript:void(0)" onclick="openModal(<?php echo e($values->id); ?>)"  class="btn btn-link"><?php echo e($values->reference_no); ?></a></td>

									<td> <?php echo e($values->relUsersList->name); ?></td>
									<td> <?php echo e($stockQty); ?></td>
									<td> <?php echo e($values->items->sum('qty')); ?></td>
									
									<td class="text-center action">
										<div class="btn-group">
											<button class="btn dropdown-toggle" data-toggle="dropdown">
												<span>
													Option
												</span>
											</button>
											<ul class="dropdown-menu">
												<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('confirm-delivery')): ?>
												<?php
													$nonNotified = RequisitionItem::doesntHave('notification')
													->where('requisition_id', $values->id)
													->count();
												?>
												<?php if($nonNotified > 0): ?>
													<li>
														<a href="<?php echo e(route('pms.store-manage.requisition.items.list',$values->id)); ?>"><?php echo e(__('Show Notification')); ?></a>
													</li>
												<?php endif; ?>

												<li><a href="<?php echo e(route('pms.store-manage.store-requistion.delivery',$values->id)); ?>" title="Click Here To Confirm Delivery" ><?php echo e(__('Confirm Delivery')); ?></a>
												</li>
												<?php endif; ?>
											</ul>
										</div>

									</td>
								</tr>
								<?php endif; ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<?php endif; ?>

								<tr>
									<td><?php echo e($sl+1); ?></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td>Total: </td>
									<td><?php echo e($totalStockQty); ?></td>
									<td><?php echo e($totalReqQty); ?></td>
									<td></td>
								</tr>
							</tbody>
						</table>

						<div class="row">
	                        <div class="col-md-12">
	                            <div class="la-1x pull-right">
	                                <?php if(count($requisition)>0): ?>
	                                <ul>
	                                    <?php echo e($requisition->links()); ?>

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


<div class="modal" id="requisitionDetailModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Requisition Details</h4>
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

	
	function openModal(requisitionId) {
		$('#tableData').load('<?php echo e(URL::to(Request()->route()->getPrefix()."/store-inventory-compare")); ?>/'+requisitionId);
		$('#requisitionDetailModal').modal('show');
	}


	(function ($) {

		$('#department_id').on('change', function () {
			let department_id=$(this).val();
			showPreloader('block');
			$.ajax({
				type: 'POST',
				url: '<?php echo e(route('pms.store-manage.rfp.department.wise.employee')); ?>',
				dataType: "json",
				data:{_token:'<?php echo csrf_token(); ?>',department_id:department_id},
				success:function (response) {
					if(response.result == 'success'){
						showPreloader('none');
						$('#requisition_by').html(response.data);
					}
				}
			});

		});

		$('#rfpSearchStoreRequisitonBtn').on('click', function () {

			let from_date=$('#from_date').val();
			let to_date=$('#to_date').val();
			let requisition_by=$('#requisition_by').val();
			let department_id=$('#department_id').val();
			let requisition_status='1';


			const searchRequisitonPagination = () => {
				let container = document.querySelector('.searchPagination');
				let pageLink = container.querySelectorAll('.page-link');
				Array.from(pageLink).map((item, key) => {
					item.addEventListener('click', (e)=>{
						e.preventDefault();
						let getHref = item.getAttribute('href');
						showPreloader('block');
						$.ajax({
							type: 'post',
							url: getHref,
							dataType: "json",
							data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date,requisition_by:requisition_by,requisition_status:requisition_status,department_id:department_id},
							success:function (data) {
								if(data.result == 'success'){
									showPreloader('none');
									$('#viewResult').html(data.body);
									searchRequisitonPagination();
								}else{
									showPreloader('none');
									notify(data.message,'error');

								}
							}
						});
					})

				});
			};
			
			if (from_date !='' || to_date !='' || department_id || requisition_by || requisition_status) {
				showPreloader('block');
				$.ajax({
					type: 'post',
					url: $(this).attr('data-src'),
					dataType: "json",
					data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date,requisition_by:requisition_by,requisition_status:requisition_status,department_id:department_id},
					success:function (data) {
						if(data.result == 'success'){
							showPreloader('none');
							$('#viewResult').html(data.body);
							searchRequisitonPagination();
						}else{
							showPreloader('none');
							$('#viewResult').html('<div class="col-md-12"><center>No Data Found!!</center></div>');

						}
					}
				});
				return false;
			}else{
				notify('Please enter data first !!','error');

			}
		});
	})(jQuery);
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/pages/store/store-rfp-requisition-list.blade.php ENDPATH**/ ?>