
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
				</ul>
			</div>

			<div class="page-content">
				<div class="">
					<div class="panel panel-info">
						<div class="panel-body">
						<table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" cellspacing="0" width="100%" id="dataTable">
								<thead>
								<tr>
									<th width="5%"><?php echo e(__('SL No.')); ?></th>
									<th><?php echo e(__('Req.Date')); ?></th>
									<th><?php echo e(__('Ref')); ?></th>
									<th><?php echo e(__('Requisition By')); ?></th>
									<th><?php echo e(__('Qty')); ?></th>
									<th><?php echo e(__('Delivery Qty')); ?></th>
									<th><?php echo e(__('Left Qty')); ?></th>
									<th><?php echo e(__('Delivery')); ?></th>
									<th class="text-center"><?php echo e(__('Option')); ?></th>
								</tr>
								</thead>
								<tbody id="viewResult">
								<?php if(count($requisitions)>0): ?>
									<?php $__currentLoopData = $requisitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr id="row<?php echo e($value->id); ?>">
											<td><?php echo e($key+1); ?></td>
											<td><?php echo e(date('d-m-Y', strtotime($value->requisition_date))); ?></td>
											<td><a href="javascript:void(0)" onclick="openModal(<?php echo e($value->id); ?>)"  class="btn btn-link"><?php echo e($value->reference_no); ?></a></td>
											<td> <?php echo e($value->relUsersList->name); ?></td>
											<td> <?php echo e($value->requisition_qty); ?></td>
											<td> <?php echo e($value->total_delivery_qty); ?></td>
											<td> <?php echo e($value->requisition_qty-$value->total_delivery_qty); ?></td>
											<td>
												<a href="<?php echo e(route('pms.store-manage.requisition-delivered-list',$value->id)); ?>" data-toggle="tooltip" title="Click here to view details" target="_blank"> Total (<?php echo e(count($value->relRequisitionDelivery)); ?>)</a>
											</td>
											<td class="text-center action">

												<?php if($value->delivery_status=='delivered'): ?>
												<span>Full Delivered</span>
												<?php else: ?>
												<div class="btn-group">
													<button class="btn dropdown-toggle" data-toggle="dropdown">
														<span id="statusName<?php echo e($value->id); ?>">
															Action
														</span>
													</button>
													<ul class="dropdown-menu">
														<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('confirm-delivery')): ?>
														<li><a href="<?php echo e(route('pms.store-manage.store-requistion.delivery',$value->id)); ?>" title="Click Here To Confirm Delivery" ><?php echo e(__('Confirm Delivery')); ?></a>
														</li>
														<?php endif; ?>
														<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('send-to-rfp')): ?>
														<?php if($value->request_status ==NULL && $value->is_po_generate=='no'): ?>
														<li id="hideFromList<?php echo e($value->id); ?>">
															<a class="sendToPurchaseDepartment" data-src="<?php echo e(route('pms.store-manage.change.action.to.rfp')); ?>" data-id="<?php echo e($value->id); ?>"  title="Send To Procurement "><?php echo e(__('Send To Procurement ')); ?>

															</a>
														</li>
														<?php endif; ?>
														<?php endif; ?>
													</ul>
												</div>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<?php endif; ?>
								</tbody>
							</table>
							<div class="row">
		                        <div class="col-md-12">
		                            <div class="la-1x pull-right">
		                                <?php if(count($requisitions)>0): ?>
		                                <ul>
		                                    <?php echo e($requisitions->links()); ?>

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
        	
            $('#searchStoreRequisitonBtn').on('click', function () {

                let from_date=$('#from_date').val();
                let to_date=$('#to_date').val();
                let requisition_by=$('#requisition_by').val();
                let requisition_status='1';

                if (from_date !='' || to_date !='' || requisition_by || requisition_status) {
                    $.ajax({
                        type: 'post',
                        url: $(this).attr('data-src'),
                        dataType: "json",
                        data:{_token:'<?php echo csrf_token(); ?>',from_date:from_date,to_date:to_date,requisition_by:requisition_by,requisition_status:requisition_status},
                        success:function (data) {
                            if(data.result == 'success'){
                                $('#viewResult').html(data.body);
                                sendToPurchaseDepartment();
                            }else{
                                //notify(data.message,'error');
                                $('#viewResult').html('<tr><td colspan="6" style="text-align: center;">No Data Found</td></tr>');

                            }
                        }
                    });
                    return false;
                }else{
                    notify('Please enter data first !!','error');

                }
            });

            const sendToPurchaseDepartment = () => {
                $('.sendToPurchaseDepartment').on('click', function () {

                    let requisition_id=$(this).attr('data-id');

                    if (requisition_id !='') {
                        swal({
                            title: "<?php echo e(__('Are you sure?')); ?>",
                            text: "<?php echo e(__('Once you send it for Procurement , You can not rollback from there.')); ?>",
                            icon: "warning",
                            dangerMode: true,
                            buttons: {
                                cancel: true,
                                confirm: {
                                    text: 'Send To Procurement ',
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
                                            $('#hideFromList'+requisition_id).hide();
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

        })(jQuery);

        function openModal(requisitionId) {
            $('#tableData').load('<?php echo e(URL::to(Request()->route()->getPrefix()."/store-inventory-compare")); ?>/'+requisitionId);
            $('#requisitionDetailModal').modal('show');
        }
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/requisition-delivery/index.blade.php ENDPATH**/ ?>