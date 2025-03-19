
<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
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
									<input type="text" name="from_date" id="from_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(old('from_date')?old('from_date'):date("d-m-Y", strtotime(date('Y-m-01')))); ?>" readonly>
								</div>
							</div>
							<div class="col-md-3 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="to_date"><?php echo e(__('To Date')); ?>:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<input type="text" name="to_date" id="to_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(old('to_date')?old('to_date'):date('d-m-Y')); ?>" readonly>
								</div>
							</div>
							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="requisition_by"><?php echo e(__('Requisition Users')); ?>:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<select name="requisition_by" id="requisition_by" class="form-control rounded">
										<option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
										<?php $__currentLoopData = $userList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($values->id); ?>"><?php echo e($values->name); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
							</div>

							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="searchRfpRequisitonBtn"></label></p>
								<div class="input-group input-group-md">
									<a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="<?php echo e(route('pms.rfp.rfp-requistion-search')); ?>" id="searchRfpRequisitonBtn"> <i class="las la-search"></i>Search</a>
								</div>
							</div>

						</div>
						<div id="viewResult">
							<table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="5%"><?php echo e(__('SL No.')); ?></th>
										<th><?php echo e(__('Unit')); ?></th>
										<th><?php echo e(__('Department')); ?></th>
										<th><?php echo e(__('Date')); ?></th>
										<th><?php echo e(__('Reference No')); ?></th>
										<th><?php echo e(__('Request By')); ?></th>
										<th><?php echo e(__('RFP Qty')); ?></th>
										<th class="text-center"><?php echo e(__('Option')); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php if(count($requisitionData)>0): ?>
									<?php $__currentLoopData = $requisitionData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr id="row<?php echo e($values->id); ?>">
										<td><?php echo e($key+1); ?></td>
										<td>
											<?php echo e($values->relUsersList->employee->unit->hr_unit_short_name?$values->relUsersList->employee->unit->hr_unit_short_name:''); ?>

										</td>
										<td>
											<?php echo e($values->relUsersList->employee->department->hr_department_name?$values->relUsersList->employee->department->hr_department_name:''); ?>

										</td>
										<td><?php echo e(date('d-m-Y', strtotime($values->requisition_date))); ?></td>
										<td><a href="javascript:void(0)" onclick="openModal(<?php echo e($values->id); ?>)"  class="btn btn-link"><?php echo e($values->reference_no); ?></a></td>
										<td><?php echo e($values->relUsersList->name); ?></td>
										
										<td>
											<?php if($values->total_delivery_qty > 0): ?>
											<?php echo e($values->requisition_qty-$values->total_delivery_qty); ?>

											<?php else: ?>
											<?php echo e($values->items->sum('qty')); ?>

											<?php endif; ?>
										</td>
										<td class="text-center action">
											<div class="btn-group">
												<button class="btn dropdown-toggle" data-toggle="dropdown">
													<span id="statusName<?php echo e($values->id); ?>">
														Status
													</span>
												</button>
												<ul class="dropdown-menu">

													<li><a class="convertToRfp" data-src="<?php echo e(route('pms.rfp.convert.to.rfp')); ?>" data-id="<?php echo e($values->id); ?>" title="Prepare to RFP" ><?php echo e(__('Prepare To RFP')); ?></a>
													</li>

													<li>
														<a target="__blank" href="<?php echo e(route('pms.rfp.send.to.purchase',$values->id)); ?>" title="Direct Purchase"><?php echo e(__('Direct Purchase')); ?>

														</a>
													</li>
												</ul>
											</div>

										</td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endif; ?>
								</tbody>

							</table>
							<div class="py-2 col-md-12">
								<?php if(count($requisitionData)>0): ?>
									<ul>
										<?php echo e($requisitionData->links()); ?>

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
	(function ($) {
		$('#searchRfpRequisitonBtn').on('click', function () {

			let from_date=$('#from_date').val();
			let to_date=$('#to_date').val();
			let requisition_by=$('#requisition_by').val();
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
							data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date,requisition_by:requisition_by,requisition_status:requisition_status},
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

				convertToRfp();
			};

			if (from_date !='' || to_date !='' || requisition_by || requisition_status) {
				showPreloader('block');
				$.ajax({
					type: 'post',
					url: $(this).attr('data-src'),
					dataType: "json",
					data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date,requisition_by:requisition_by,requisition_status:requisition_status},
					success:function (data) {
						if(data.result == 'success'){
							showPreloader('none');
							$('#viewResult').html(data.body);
							searchRequisitonPagination();
						}else{
							showPreloader('none');
							
							$('#viewResult').html('<tr><td colspan="7" style="text-align: center;">No Data Found</td></tr>');
						}
					}
				});
				return false;
			}else{
				notify('Please enter data first !!','error');

			}
		});

		const convertToRfp = () => {
			$('.convertToRfp').on('click', function () {

				let requisition_id=$(this).attr('data-id');

				if (requisition_id !='') {
					swal({
						title: "<?php echo e(__('Are you sure?')); ?>",
						text: "",
						icon: "warning",
						dangerMode: true,
						buttons: {
							cancel: true,
							confirm: {
								text: 'Prepare To RFP',
								value: true,
								visible: true,
								closeModal: true
							},
						},
					}).then((value) => {
						if(value){

							$.ajax({
								type: 'POST',
								url: $(this).attr('data-src'),
								dataType: "json",
								data:{_token:'<?php echo csrf_token(); ?>',requisition_id:requisition_id},
								success:function (data) {
									if(data.result == 'success'){
										$('#row'+requisition_id).hide();
										notify(data.message,'success');
									}else{
										notify(data.message,data.result);
									}
								}
							});
							return false;
						}

					});

				}else{
					notify('Please Select Requisitoin!!','error');
				}
			});
		};

		convertToRfp();

	})(jQuery);

	function openModal(requisitionId) {
		$('#tableData').load('<?php echo e(URL::to(Request()->route()->getPrefix()."/store-inventory-compare")); ?>/'+requisitionId);

		$('#requisitionDetailModal .modal-title').html('Requisition Details');
		$('#requisitionDetailModal').modal('show');
	}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/rfp/deft-requisition-index.blade.php ENDPATH**/ ?>