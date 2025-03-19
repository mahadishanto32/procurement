<?php
$corporateAddress = \App\Models\PmsModels\SupplierAddress::where(['supplier_id' => isset($purchaseOrder->relQuotation->relSuppliers->id) ? $purchaseOrder->relQuotation->relSuppliers->id : 0, 'type' => 'corporate'])->first();
$contactPersonSales = \App\Models\PmsModels\SupplierContactPerson::where(['supplier_id' => isset($purchaseOrder->relQuotation->relSuppliers->id) ? $purchaseOrder->relQuotation->relSuppliers->id : 0, 'type' => 'sales'])->first();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Purchase Order</title>
	<link rel="shortcut icon" href="<?php echo e(asset('images/mbm.ico')); ?> " />
	<link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet" media='screen,print'>
	<link rel="stylesheet" href="<?php echo e(asset('assets/css/all.css')); ?>" media='screen,print'>
	<?php echo $__env->yieldPushContent('css'); ?>
	<link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css?v=1.3')); ?>" media='screen,print'>
	<!-- Responsive CSS -->
	<link rel="stylesheet" href="<?php echo e(asset('assets/css/responsive.css')); ?>" media='screen,print'>
	<style type="text/css">
		@media  print {
			.print_the_pages {
				display: none;
			}

			/* .full-height{
				height:100% !important;
			}

			.print-header {
				width: 100%;
			    position: fixed;
			    top: 0;
			    left: 0;
			}

			.print-header-left{
				padding-left: 100px;
			}

			.print-header-right{
				padding-right: 70px;
			}

			.print-body{
				padding-top:  75px !important;
			}

			.print-footer {
				position: absolute;
  				bottom: 0;
			}*/
		}

	    .list-unstyled .ratings {
	        display: none;
	    }
	</style>
</head>
<body>
	<div id="app">

		<div id="content-page" class="container">
			<main class="" style="padding-bottom: 0;">
				<div id="main-body" class="">
					<div class="main-content">
						<div class="main-content-inner">
							<?php if(isset($purchaseOrder)): ?>
							<div class="row">
							<div class="col-md-12 p-30 full-height" id="print_invoice">

								<div class="panel panel-body">
									<div class="row mb-3 print-header">
										<div class="col-md-6 pt-5 print-header-left">
											<h2><strong>Purchase Order</strong></h2>
										</div>
										<div class="col-md-6  print-header-right text-right">
											<?php if(!empty($purchaseOrder->Unit->hr_unit_logo)): ?>
											<img src="<?php echo e(asset($purchaseOrder->Unit->hr_unit_logo)); ?>" alt="" style="height: 100px;width: 250px;">
											<?php else: ?>
											<img src="<?php echo e(asset($purchaseOrder->Unit->hr_unit_logo)); ?>" alt="" style="height: 100px;width: 250px;">
											<?php endif; ?>
										</div>
									</div>

									<div class="print-body">
										<div class="table-responsive">
											<table class="table table-bordered">
												<tbody>
													<tr>
														<td style="width: 50% !important">
															<h5 class="mb-0"><strong>Vendor Name:&nbsp;<?php echo e(isset($purchaseOrder->relQuotation->relSuppliers->name) ? $purchaseOrder->relQuotation->relSuppliers->name : ''); ?></strong></h5>
														</td>
														<td style="width: 50% !important;text-align: right !important">
															<img src="data:image/png;base64,<?php echo DNS1D::getBarcodePNG($purchaseOrder->reference_no, 'C39',1,33); ?>" alt="barcode" style="float: right" />
														</td>
													</tr>
													<tr>
														<td style="width: 50% !important;font-size: 14px !important;">
															Address:&nbsp;
															<?php if(isset($corporateAddress->id)): ?>
															<?php echo e($corporateAddress->road.' '.$corporateAddress->village.', '.$corporateAddress->city.'-'.$corporateAddress->zip.', '.$corporateAddress->country); ?>

															<br>
															<?php echo e($corporateAddress->adddress); ?>

															<?php endif; ?>
														</td>
														<td style="width: 50% !important;font-size: 14px !important;" class="text-right">
															PO Ref. No:&nbsp;<?php echo e($purchaseOrder->reference_no); ?>

															<br>
															PO Date:&nbsp;<?php echo e(date('jS F Y', strtotime($purchaseOrder->po_date))); ?>

															<br>
															Quotation Ref. No:&nbsp;<?php echo e(isset($purchaseOrder->relQuotation->id) ? $purchaseOrder->relQuotation->reference_no : ''); ?>

														</td>
													</tr>
													<tr>
														<td style="width: 50% !important;font-size: 14px !important;">
															Attention:&nbsp;
															<?php if(isset($contactPersonSales->id)): ?>
															<?php echo e($contactPersonSales->name.', '.$contactPersonSales->designation); ?>,
															<br>
															Mobile:&nbsp;<?php echo e($contactPersonSales->mobile); ?>,
															<br> 
															Mail:&nbsp;<?php echo e($contactPersonSales->email); ?>

															<?php endif; ?>
														</td>
														<td style="width: 50% !important;font-size: 14px !important;" class="text-right">
															Delivery Location:&nbsp;
															<?php echo e($purchaseOrder->Unit->hr_unit_name); ?>

															<div>
																<?php echo $purchaseOrder->Unit->hr_unit_address?$purchaseOrder->Unit->hr_unit_address:''; ?>

															</div>
														</td>
													</tr>
													<tr>
														<td style="width: 50% !important;font-size: 14px !important;font-weight: bold !important">
															Payment Mode:&nbsp;<?php echo e(isset($purchaseOrder->relQuotation->relSupplierPaymentTerm->relPaymentTerm->term) ? $purchaseOrder->relQuotation->relSupplierPaymentTerm->relPaymentTerm->term : ''); ?>

														</td>
														<td style="width: 50% !important;font-size: 14px !important;" class="text-right">
															Delivery Contact:&nbsp;<?php echo isset($deliveryContact->id) ? $deliveryContact->name.'&nbsp;&nbsp;|&nbsp;&nbsp;'.($deliveryContact->employee ? ($deliveryContact->employee->designation ? $deliveryContact->employee->designation->hr_designation_name : '') : '').'&nbsp;&nbsp;|&nbsp;&nbsp;'.$deliveryContact->phone : ''; ?>

														</td>
													</tr>
												</tbody>
											</table>
										</div>

										<div class="table-responsive">
											<table class="table table-bordered">
												<thead>
													<tr>
														<th>SL</th>
														<th>Product</th>
														<th>Unit</th>
														<th style="width: 12% !important">Unit Price</th>
														<th>Qty</th>
														<th>Price</th>
													</tr>
												</thead>
												<tbody>
													<?php $__currentLoopData = $purchaseOrder->relPurchaseOrderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<tr>
														<td><?php echo e($key+1); ?></td>
														<td><?php echo e($item->relProduct->name); ?> (<?php echo e(getProductAttributes($item->product_id)); ?>)</td>
														<td><?php echo e($item->relProduct->productUnit->unit_name); ?></td>
														<td class="text-right"><?php echo e($item->unit_price); ?></td>
														<td class="text-right"><?php echo e(number_format($item->qty,0)); ?></td>
														<td class="text-right"><?php echo e(number_format($item->sub_total_price,2)); ?></td>
													</tr>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

													<tr>
														<td colspan="3" class="text-right">Total</td>
														<td colspan="" class="text-right"><?php echo e(number_format($purchaseOrder->relPurchaseOrderItems->sum('unit_price'),2)); ?></td>
														<td colspan="" class="text-right" class="text-right"><?php echo e($purchaseOrder->relPurchaseOrderItems->sum('qty')); ?></td>
														<td colspan="" class="text-right"><?php echo e(number_format($purchaseOrder->relPurchaseOrderItems->sum('sub_total_price'),2)); ?></td>
													</tr>

													<tr>
														<td colspan="5" class="text-right">(-) Discount</td>
														<td class="text-right"><?php echo e(number_format($purchaseOrder->discount,2)); ?></td>
													</tr>
													<tr>
														<td colspan="5" class="text-right">(+) Vat </td>
														<td class="text-right"><?php echo e(number_format($purchaseOrder->vat,2)); ?></td>
													</tr>
													<tr>
														<td colspan="5" class="text-right"><strong>Total Amount</strong></td>
														<td class="text-right"><strong><?php echo e(number_format($purchaseOrder->gross_price,2)); ?></strong></td>
													</tr>
												</tbody>
											</table>
											In word: Taka <strong><?php echo e(inWord($purchaseOrder->gross_price)); ?></strong> only
										</div>
										
										

										<div class="form-group">
											<label for="terms-condition"><strong>Terms & Conditions</strong>:</label>
											<div class="pl-4"><?php echo isset($purchaseOrder->relQuotation->relSuppliers->term_condition) ? $purchaseOrder->relQuotation->relSuppliers->term_condition : ''; ?></div>
										</div>
									</div>
									

									

									<div class="print-footer" id="footer">
										<div class="form-group text-center pt-2 pb-2">
											PO Issued by <strong><?php echo e(auth()->user()->name); ?></strong>
										</div>

										<div class="form-group">
											<small>(Note: This purchase order doesn’t require signature as it is automatically generated from TECHNOCRATS ERP)</small>
										</div>

										<div class="form-group row pt-2" style="border-top: 3px solid black">
											<div class="col-md-6" style="border-right: 1px solid black">
												Factory: DOHS, Mohakahli, Dhaka-1216
												<br>
												Phone: +88011223344, Mail: info@technocrats.com.bd
											</div>
											<div class="col-md-6 pl-5" style="border-left: 2px solid black">
												Corporate Office: DOHS, Mohakahli, Dhaka-1216
												<br>
												Avenue: DOHS, Mohakahli, Dhaka-1216
												<br>
												Website: www.technocrats.com.bd
											</div>
										</div>
									</div>

								</div>
							</div>
							<div class="col-md-12 mb-3">
								<center>
									<a href="#" class="btn btn-info btn-sm print_the_pages text-center">
										<i class="las la-print" aria-hidden="true"></i>
										<span>Print Invoice</span></a>
									</center>
								</div>
							</div>
							<?php endif; ?>

						</div>
					</div>
				</div>
			</main>
		</div>
	</div>



</body>
<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/all.js')); ?>"></script>
<script>
	window.print();
	
	var count = 0;
	var refreshIntervalId =setInterval(function(){
		count++;
		jQuery(document).ready(function() {
			clearInterval(refreshIntervalId);
			jQuery("#load").fadeOut();
			jQuery("#loading").fadeOut("");

		});
		if( count == 5){
			clearInterval(refreshIntervalId);
			jQuery("#load").fadeOut();
			jQuery("#loading").fadeOut("");
		}
	}, 300);

	var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';
	let afterLoader = '<div class="loading-select left"><img src="<?php echo e(asset('images/loader.gif')); ?>" /></div>';

	const PrintPage=()=>{
		$('.print_the_pages').on('click', function () {
			var restorepage = $('body').html();
			var printcontent = $('#print_invoice').clone();
			$('body').empty().html(printcontent);
			window.print();
			$('body').html(restorepage)

			return false;
		}); 
	};
	PrintPage();
</script>
</html><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/billing/po-invoice-print.blade.php ENDPATH**/ ?>