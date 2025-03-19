
<?php $__env->startSection('title', config('app.name', 'laravel'). ' | '.$title); ?>
<?php $__env->startSection('page-css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
<?php
use App\Models\PmsModels\InventoryModels\InventoryDetails;
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
						<form action="<?php echo e(url('pms/inventory/inventory-summary')); ?>" method="get" accept-charset="utf-8">
							<div class="row">
								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="category_id"><?php echo e(__('Category')); ?>:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<select name="category_id" id="category_id" onchange="getSubCategory()" class="form-control rounded">
											<option value="0"><?php echo e(__('Select One')); ?></option>
											<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<option value="<?php echo e($values->id); ?>" <?php echo e($category_id==$values->id ? 'selected' : ''); ?>><?php echo e($values->name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</select>
									</div>
								</div>
								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="sub_category_id"><?php echo e(__('Sub Category')); ?>:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<select name="sub_category_id" id="sub_category_id" onchange="getProduct()" class="form-control rounded">

										</select>
									</div>
								</div>
								<div class="col-md-3">
									<p class="mb-1 font-weight-bold"><label for="product_id"><?php echo e(__('Product')); ?>:</label></p>
									<div class="input-group input-group-md mb-3">
										<select class="form-control rounded" name="product_id" id="product_id">

										</select>
									</div>
								</div>

								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="searchRequisitonBtn"></label></p>
									<button type="submit" class="btn btn-sm btn-success mt-8"> <i class="las la-search"></i>Search</button>
									<a class="btn btn-sm btn-danger mt-8" href="<?php echo e(url('pms/inventory/inventory-summary')); ?>"> <i class="las la-times"></i>Reset</a>
								</div>
							</div>
						</form>

						<div class="table-responsive style-scroll">
							<table class="table table-striped table-bordered miw-500 dac_table datatable-exportable" data-table-name="<?php echo e($title); ?>" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="5%"><?php echo e(__('SL No.')); ?></th>
										<th><?php echo e(__('Category')); ?></th>
										<th><?php echo e(__('Product')); ?></th>
										
										<th><?php echo e(__('Qty')); ?></th>
										
									</tr>
								</thead>
								<tbody>
									<?php if(isset($inventories[0])): ?>
									<?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php
									$summariesQty=InventoryDetails::where('product_id',$values->product_id)->where('hr_unit_id',auth()->user()->employee->as_unit_id)->sum('qty');
									?>
									<tr>
										<td><?php echo e($key+1); ?></td>
										<td><?php echo e($values->relCategory->name); ?></td>
										<td>
											<a href="javascript:void(0)" onclick="openModal(<?php echo e($values->relProduct->id); ?>)"  class="btn btn-link">
												<?php echo e($values->relProduct->name); ?> (<?php echo e(getProductAttributes($values->product_id)); ?>)
											</a>
										</td>
										
										<td><?php echo e($summariesQty); ?></td>
										
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endif; ?>
								</tbody>
							</table>

							<div class="row">
			                    <div class="col-md-12">
			                        <div class="la-1x pull-right">
			                            <?php if(count($inventories)>0): ?>
			                            <ul>
			                                <?php echo e($inventories->links()); ?>

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

<div class="modal" id="warehouseDetailModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Inventory Details</h4>
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
	getSubCategory();
	function getSubCategory(){
		var category_id = $('#category_id').val();
		var sub_category_id = "<?php echo e($sub_category_id); ?>";
		$('#sub_category_id').html('<option value="0">Select One</option>');

		$.ajax({
			url: "<?php echo e(url('pms/inventory/inventory-summary')); ?>/"+category_id+"/get-products?sub-categories",
			type: 'GET',
			dataType: 'json',
			data: {},
		})
		.done(function(response) {
			var subCategories = '<option value="0">Select One</option>';
			$.each(response, function(index, val) {
				var selected = (sub_category_id == val.id ? 'selected' : '');
				subCategories += '<option value="'+(val.id)+'" '+(selected)+'>'+(val.name)+'</option>';
			});

			$('#sub_category_id').html(subCategories);
			getProduct();
		});
	}

	function getProduct(){
		var category_id = $('#category_id').val();
		var sub_category_id = $('#sub_category_id').val();
		var product_id = "<?php echo e($product_id); ?>";
		$('#product_id').html('<option value="0">Select One</option>');

		$.ajax({
			url: "<?php echo e(url('pms/inventory/inventory-summary')); ?>/"+category_id+"/get-products?sub_category_id="+sub_category_id,
			type: 'GET',
			dataType: 'json',
			data: {},
		})
		.done(function(response) {
			var product = '<option value="0">Select One</option>';
			$.each(response.products, function(index, val) {
				var selected = '';
				if(product_id == val.id){
					selected = 'selected';
				}
				product += '<option value="'+(val.id)+'" '+(selected)+'>'+(val.name)+' ('+(response.attributes[val.id])+')</option>';
			});

			$('#product_id').html(product);
		});
	}

	function openModal(product_id) {
		$('#tableData').load('<?php echo e(URL::to('pms/inventory/warehouse-wise-product-inventory-details')); ?>/'+product_id);
		$('#warehouseDetailModal').modal('show');
	}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pms.backend.layouts.master-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/inventory/inventory-summary/index.blade.php ENDPATH**/ ?>