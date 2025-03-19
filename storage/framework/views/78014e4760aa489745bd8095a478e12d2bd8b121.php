
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
						<form action="<?php echo e(url('pms/accounts/supplier-ledgers')); ?>" method="get" accept-charset="utf-8">
							<div class="row">
								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="from_date"><?php echo e(__('From Date')); ?>:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<input type="text" name="from_date" id="from_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e($from_date); ?>" readonly>
									</div>
								</div>
								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="to_date"><?php echo e(__('To Date')); ?>:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<input type="text" name="to_date" id="to_date" class="form-control search-datepicker rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="<?php echo e($to_date); ?>" readonly>
									</div>
								</div>

								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="supplier_id"><?php echo e(__('Supplier')); ?>:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<select name="supplier_id" id="supplier_id" class="form-control rounded">
											<option value="<?php echo e(null); ?>"><?php echo e(__('Select One')); ?></option>
											<?php if(isset($chooseSuppliers[0])): ?>
											<?php $__currentLoopData = $chooseSuppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($supplier->id); ?>" <?php echo e($supplier_id == $supplier->id ? 'selected' : ''); ?>><?php echo e($supplier->name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										</select>
									</div>
								</div>

								<div class="col-md-2 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="searchRequisitonBtn"></label></p>
									<div class="input-group input-group-md">
										<button class="btn btn-success rounded mt-8"><i class="las la-search"></i>Search</button>
									</div>
								</div>
							</div>
						</form>

						<table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="<?php echo e($title); ?>" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="8%"><?php echo e(__('SL No.')); ?></th>
										<th><?php echo e(__('Transaction Date')); ?></th>
										<th><?php echo e(__('Opening Balance')); ?></th>
										<th><?php echo e(__('Debit')); ?></th>
										<th><?php echo e(__('Credit')); ?></th>
										<th><?php echo e(__('Closing Balance')); ?></th>
									</tr>
								</thead>
								<tbody>
								<?php if(isset($ledgers[0])): ?>
								<?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr id="removeRow<?php echo e($values->id); ?>">
									<td>
										<?php echo e($key + 1); ?>

									</td>
									<td><?php echo e(date('d-M-Y', strtotime($values->date))); ?></td>
									<td class="text-right"><?php echo e(number_format($values->opening_balance, 2)); ?></td>
									<td class="text-right"><?php echo e(number_format($values->debit, 2)); ?></td>
									<td class="text-right"><?php echo e(number_format($values->credit, 2)); ?></td>
									<td class="text-right"><?php echo e(number_format($values->closing_balance, 2)); ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<?php endif; ?>
							</tbody>
						</table>

						<div class="row">
		                    <div class="col-md-12">
		                        <div class="la-1x pull-right">
		                            <?php if(count($ledgers)>0): ?>
		                            <ul>
		                                <?php echo e($ledgers->links()); ?>

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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
<script>
	 (function ($) {
        "use script";

        const showPODetails = () => {
            $('.showPODetails').on('click', function () {

                $.ajax({
                    url: $(this).attr('data-src'),
                    type: 'get',
                    dataType: 'json',
                    data: '',
                })
                .done(function(response) {

                    if (response.result=='success') {
                        $('#POdetailsModel').find('#body').html(response.body);
                        $('#POdetailsModel').find('.modal-title').html(`Purchase Order Details`);
                        $('#POdetailsModel').modal('show');
                    }

                })
                .fail(function(response){
                    notify('Something went wrong!','error');
                });
            });
        }
        showPODetails();

        /*$('.GenerateLedger').on('click', function () {
        
        let id = $(this).attr('data-id');
        let dataSrc = $(this).attr('data-src');
        let parentDiv = $(this).parent();
        
        let texStatus='Submit';
        let textContent='Are you sure to generate this ledger?';

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
                    url: dataSrc,
                    type: 'POST',
                    dataType: 'json',
                    data: {_token: "<?php echo e(csrf_token()); ?>", id:id},
                })
                .done(function(response) {
                    if(response.success){
                        parentDiv.html('<span class="btn btn-sm btn-warning">'+response.new_text+'</span>');
                        notify(response.message,'success')
                        
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
    });*/

    $('.page-link').click(function(event) {
		event.preventDefault();

		window.open($(this).attr('href')+"&from_date=<?php echo e($from_date); ?>&to_date=<?php echo e($to_date); ?>&supplier_id=<?php echo e($supplier_id); ?>", "_parent");
	});

    })(jQuery);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/accounts/supplier-ledger-list.blade.php ENDPATH**/ ?>