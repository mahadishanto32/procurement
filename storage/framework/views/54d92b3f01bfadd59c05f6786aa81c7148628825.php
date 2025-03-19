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
									<input type="text" name="to_date" id="to_date" class="form-control search-datepicker rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e(old('to_date')?old('to_date'):date('d-m-Y')); ?>" readonly>
								</div>
							</div>
							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="requisition_by"><?php echo e(__('Requisition By')); ?>:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<select name="requisition_by" id="requisition_by" class="form-control rounded">
										<option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
										<?php $__currentLoopData = $requisitionUserLists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($values->id); ?>"><?php echo e($values->name); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
							</div>
							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="requisition_status"><?php echo e(__('Status')); ?>:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<select name="requisition_status" id="requisition_status" class="form-control rounded">
										<option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
										<?php $__currentLoopData = statusArrayForHead(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($key); ?>"><?php echo e($values); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
							</div>
							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="searchRequisitonBtn"></label></p>
								<div class="input-group input-group-md">
									<a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="<?php echo e(route('pms.requisition.list.view.search')); ?>" id="searchRequisitonBtn"> <i class="las la-search"></i>Search</a>
								</div>
							</div>

						</div>

						<div id="viewResult">
							<table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" border="0" id="dataTable">
								<thead>
									<tr>
										<th width="5%"><?php echo e(__('SL No.')); ?></th>
										<th><?php echo e(__('Unit')); ?></th>
										
										<th><?php echo e(__('Date')); ?></th>
										<th><?php echo e(__('Reference No')); ?></th>
										<th><?php echo e(__('Requisition By')); ?></th>
										
										<th class="text-center"><?php echo e(__('Option')); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php if(count($requisitionData)>0): ?>
									<?php $__currentLoopData = $requisitionData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php 
									$createdDate=date('Y-m-d',strtotime($values->created_at));
									$requisitionDate=date('Y-m-d',strtotime($values->requisition_date));
								?>
								<tr id="removeRow<?php echo e($values->id); ?>" class="<?php echo e(($createdDate==$requisitionDate)?'':'bg-warning color-white'); ?>">
									<td><?php echo e(($requisitionData->currentpage()-1) * $requisitionData->perpage() + $key + 1); ?></td>
									<td>
										<?php echo e($values->relUsersList->employee->unit->hr_unit_short_name?$values->relUsersList->employee->unit->hr_unit_short_name:''); ?>

									</td>
									
									<td><?php echo e(date('d-m-Y', strtotime($values->requisition_date))); ?></td>

									<td><a href="javascript:void(0)" data-src="<?php echo e(route('pms.requisition.list.view.show',$values->id)); ?>"  class="btn btn-link showRequistionDetails"><?php echo e($values->reference_no); ?></a></td>

									<td><?php echo e($values->relUsersList->name); ?></td>
									<td class="text-center action">
										<div class="btn-group">
											<button class="btn dropdown-toggle" data-toggle="dropdown">
												<span id="statusName<?php echo e($values->id); ?>">
													<?php if($values->status==0): ?>
													<?php echo e(__('Pending')); ?>

													<?php elseif($values->status==1): ?>
													<?php echo e(__('Acknowledge')); ?>

													<?php else: ?>
													<?php echo e(__('Halt')); ?>

													<?php endif; ?>
												</span>
											</button>
											<ul class="dropdown-menu">

												<?php if($values->is_send_to_rfp=='no'): ?>
												<?php if($values->status !=0): ?>
												<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pending')): ?>
												<li><a href="javascript:void(0)" title="Click Here To Pending" class="requisitionApprovedBtn" data-id="<?php echo e($values->id); ?>"  data-data="pending" data-status="0"><?php echo e(__('Pending')); ?></a>
												</li>
												<?php endif; ?>
												<?php endif; ?>

												<?php if($values->status !=1): ?>
												<li><a href="<?php echo e(route('pms.requisition.requisition.edit',$values->id)); ?>" title="Click Here To Edit"><i class="la la-edit"></i><?php echo e(__('Edit')); ?></a>
												</li>
												<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('requisition-acknowledge')): ?>
												<li><a href="javascript:void(0)" title="Click Here To Acknowledge" class="requisitionApprovedBtn" data-id="<?php echo e($values->id); ?>" data-data="acknowledged" data-status="1"><?php echo e(__('Acknowledge')); ?></a>
												</li>
												<?php endif; ?>
												<?php endif; ?>

												<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('halt')): ?>
												<li><a href="javascript:void(0)" title="Click Here To Halt" class="requisitionApprovedBtn"  data-data="halt" data-id="<?php echo e($values->id); ?>" data-status="2"><?php echo e(__('Halt')); ?></a>
												</li>
												<?php endif; ?>
												<?php endif; ?>
												<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('send-to-rfp')): ?>
												<li>
													<a class="sendToPurchaseDepartment" data-src="<?php echo e(route('pms.store-manage.send.to.purchase.department')); ?>" data-id="<?php echo e($values->id); ?>"  title="Send To procurement "><?php echo e(__('Send To Procurement ')); ?>

													</a>
												</li>
												<?php endif; ?>

											</ul>
										</div>

									</td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<?php endif; ?>
							</tbody>

						</table>
						<div class="p-3">
							<?php if(count($requisitionData)>0): ?>
							<?php echo e($requisitionData->links()); ?>

							<?php endif; ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
</div>


<!--Start Requisition Status Change Modal -->
<div class="modal" id="requisitionHoldModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Halt the Requisition</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<!-- Modal body -->
			<form action="<?php echo e(route('pms.requisition.halt.status')); ?>" method="POST">
				<?php echo csrf_field(); ?>
				<div class="modal-body">
					<div class="form-group">
						<label for="admin_remark">Notes:</label>

						<input type="hidden" readonly required name="id" id="requisitionId">
						<textarea class="form-control" required name="admin_remark" rows="3" id="admin_remark" placeholder="Write here...."></textarea>
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
<!--End Requisition Status Change Modal -->
<!--Start Acknowledge Status -->
<div class="modal" id="requisitionAckModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Acknowledge Requisition</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<!-- Modal body -->
			<form action="<?php echo e(url('pms/requisition/approved-status')); ?>" method="POST">
				<?php echo csrf_field(); ?>
				<div class="modal-body">
					<div class="form-group">
						<label for="admin_remark">Notes (Optional):</label>

						<textarea class="form-control" name="admin_remark" rows="3" id="admin_remark" placeholder="Write here..."></textarea>
						<input type="hidden" readonly required name="id" id="requisitionApprovedId">
						<input type="hidden" readonly required name="status" id="requisitionStatus">
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
<!--End Acknowledge Status -->

<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
<script>
// $('#dataTable').DataTable();
(function ($) {
	"use script";
//Search 
$('#searchRequisitonBtn').on('click', function () {
	let from_date=$('#from_date').val();
	let to_date=$('#to_date').val();
	let requisition_by=$('#requisition_by').val();
	let requisition_status=$('#requisition_status').val();

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
		approvedRequistion();
		sendToPurchaseDepartment();
		showRequistionDetails();
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
					$('#viewResult').html('<div class="col-md-12"><center>No Data Found!!</center></div>');
				}	
			}
		});
		return false;
	}else{
		notify('Please enter data first !!','error');

	}
});
//Approved Reject 
const approvedRequistion = () => {
	$('.requisitionApprovedBtn').on('click', function () {
		let id = $(this).attr('data-id');
		let status = $(this).attr('data-status');

		if (status==1){
			$('#requisitionApprovedId').val(id);
			$('#requisitionStatus').val(status);
			return $('#requisitionAckModal').modal('show');
		}else if (status==2){
			$('#requisitionId').val(id)
			return $('#requisitionHoldModal').modal('show');
		}else if(status==0){
			texStatus='Pending';
			textContent='Do you want to pending this requisition?';
		}

		swal({
			title: "<?php echo e(__('Are you sure?')); ?>",
			text: textContent,
			icon: "warning",
			dangerMode: true,
			buttons: {
				cancel: true,
				confirm: {
					text: texStatus,
					value: true,
					visible: true,
					closeModal: true
				},
			},
		}).then((value) => {
			if(value){
				$.ajax({
					url: "<?php echo e(url('pms/requisition/approved-status')); ?>",
					type: 'POST',
					dataType: 'json',
					data: {_token: "<?php echo e(csrf_token()); ?>", id:id, status:status},
				})
				.done(function(response) {
					if(response.success){
						$('#statusName'+id).html(response.new_text);
						$('#removeRow'+id).hide();
						notify(response.message,'success');
					}else{
						notify(response.message,'error');
					}
				})
				.fail(function(response){
					notify('Something went wrong!','error');
				});

				return false;
			}
		});
	});
};
approvedRequistion();

const capitalize = s => (s && s[0].toUpperCase() + s.slice(1)) || "";

//send to purchase department
const sendToPurchaseDepartment = () => {
	$('.sendToPurchaseDepartment').on('click', function () {

		let requisition_id=$(this).attr('data-id');

		if (requisition_id !='') {
			swal({
				title: "<?php echo e(__('Are you sure?')); ?>",
				text: "<?php echo e(__('Once you send it for RFP, You can not rollback from there.')); ?>",
				icon: "warning",
				dangerMode: true,
				buttons: {
					cancel: true,
					confirm: {
						text: 'Send To RFP',
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

sendToPurchaseDepartment();

const showRequistionDetails = () => {
	$('.showRequistionDetails').on('click', function (e) {
		$.ajax({
			url: e.target.getAttribute('data-src'),
			type: 'get',
			dataType: 'json',
			data: '',
			success: function (response) {
				$('#requisitionDetailModal').find('#tableData').html(response);
				$('#requisitionDetailModal').find('.modal-title').html(`Requisition Details`);
				$('#requisitionDetailModal').modal('show');
			}
		});
	});
};
showRequistionDetails();
})(jQuery);



</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/sites/34b/a/a55a29c214/procurement.zbridea.com/resources/views/pms/backend/pages/requisitions/requisition-list-index.blade.php ENDPATH**/ ?>