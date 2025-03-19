<?php
$corporateAddress = \App\Models\PmsModels\SupplierAddress::where(['supplier_id' => isset($quotation->relPurchaseOrder->relQuotation->relSuppliers->id) ? $quotation->relPurchaseOrder->relQuotation->relSuppliers->id : 0, 'type' => 'corporate'])->first();
$contactPersonSales = \App\Models\PmsModels\SupplierContactPerson::where(['supplier_id' => isset($quotation->relPurchaseOrder->relQuotation->relSuppliers->id) ? $quotation->relPurchaseOrder->relQuotation->relSuppliers->id : 0, 'type' => 'sales'])->first();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo e($title); ?></title>
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
		}
		* {
			font-size: 16px !important;
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
							<?php if(isset($quotation->relPurchaseOrder->relQuotation)): ?>
							<div class="row">
								<div class="col-md-12 p-30" id="print_invoice">

									<div class="panel panel-body">
										<div class="row mb-3">
											<div class="col-md-6 text-left">
												<h3>Return Approved List</h3>
											</div>
											<div class="col-md-6 text-right">
											<?php if(!empty($quotation->relPurchaseOrder->Unit->hr_unit_logo)): ?>
												<img src="<?php echo e(asset($quotation->relPurchaseOrder->Unit->hr_unit_logo)); ?>" alt="" style="height: 100px;width: 250px;">
												<?php else: ?>
												<img src="<?php echo e(asset('images/mbm.png')); ?>" alt="" style="height: 100px;width: 250px;">
												<?php endif; ?>
											</div>
										</div>
										<div class="table-responsive">
											<table class="table table-bordered">
												<tbody>
													<tr>
														<td style="width: 50% !important">
															<h5 class="mb-0"><strong>Vendor Name:&nbsp;<?php echo e(isset($quotation->relPurchaseOrder->relQuotation->relSuppliers->name) ? $quotation->relPurchaseOrder->relQuotation->relSuppliers->name : ''); ?></strong></h5>
														</td>
														<td style="width: 50% !important;text-align: right !important">
															<img src="data:image/png;base64,<?php echo DNS1D::getBarcodePNG($quotation->relPurchaseOrder->relQuotation->reference_no, 'C39',1,33); ?>" alt="barcode" style="float: right" />
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
															PO Ref. No:&nbsp;<?php echo e($quotation->relPurchaseOrder->reference_no); ?>

															<br>
															PO Date:&nbsp;<?php echo e(date('jS F Y', strtotime($quotation->relPurchaseOrder->po_date))); ?>

															<br>
															Quotation Ref. No:&nbsp;<?php echo e(isset($quotation->relPurchaseOrder->relQuotation->id) ? $quotation->relPurchaseOrder->relQuotation->reference_no : ''); ?>

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
															<?php echo e($quotation->relPurchaseOrder->Unit->hr_unit_name); ?>

															<div>
																<?php echo $quotation->relPurchaseOrder->Unit->hr_unit_address?$quotation->relPurchaseOrder->Unit->hr_unit_address:''; ?>

															</div>
														</td>
													</tr>
													<tr>
														<td style="width: 50% !important;font-size: 14px !important;font-weight: bold !important">
															Payment Mode:&nbsp;<?php echo e(isset($quotation->relPurchaseOrder->relQuotation->relSupplierPaymentTerm->relPaymentTerm->term) ? $quotation->relPurchaseOrder->relQuotation->relSupplierPaymentTerm->relPaymentTerm->term : ''); ?>

														</td>
														<td style="width: 50% !important;font-size: 14px !important;" class="text-right">
															Delivery Contact:&nbsp;<?php echo isset($quotation->relPurchaseOrder->Unit->hr_unit_telephone) ? $quotation->relPurchaseOrder->Unit->hr_unit_telephone : ''; ?>

														</td>
													</tr>
												</tbody>
											</table>

											<table class="table table-bordered">
												<thead>
													<tr>
														<th>Sl No.</th>
														
														<th>Product</th>
														<th>Unit</th>
														<th style="width: 12% !important">Unit Price</th>
														<th>Qty</th>
														<th style="width: 15% !important">Received Qty</th>
														<th style="width: 12% !important">Return Qty</th>
														<th>Price</th>
													</tr>
												</thead>
												<tbody>
													<?php 
													$sumOfReceivedtQty=0;
													$sumOfReturntQty=0;
													$sumOfItemQty=0;
													$sumOfSubtotal=0;

													$discountAmount= 0;
													$vatAmount= 0;

													?>
													<?php if(isset($approved)): ?>
													<?php $__currentLoopData = $approved; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<?php 
													$sumOfReceivedtQty +=($item->received_qty);
													$sumOfItemQty +=($item->relGoodsReceivedItems->qty);
													$sumOfReturntQty +=($item->relGoodsReceivedItems->qty-$item->received_qty);
													$sumOfSubtotal += $item->unit_amount*$item->received_qty;

													$discountAmount +=($item->discount_percentage * $item->unit_amount*$item->received_qty)/100;

													$vatAmount +=($item->vat_percentage * $item->unit_amount*$item->received_qty)/100;
													?>

													<tr id="removeApprovedRow<?php echo e($item->id); ?>">
														<td><?php echo e($key+1); ?></td>
														
														<td><?php echo e($item->relGoodsReceivedItems->relProduct->name); ?> (<?php echo e(getProductAttributes($item->relGoodsReceivedItems->relProduct->id)); ?>)</td>
														<td><?php echo e($item->relGoodsReceivedItems->relProduct->productUnit->unit_name); ?></td>
														<td><?php echo e($item->unit_amount); ?></td>
														<td><?php echo e(number_format($item->relGoodsReceivedItems->qty,0)); ?></td>
														<td><?php echo e($item->received_qty); ?></td>
														<td><?php echo e($item->relGoodsReceivedItems->qty-$item->received_qty); ?></td>
														<td><?php echo e(number_format($item->unit_amount*$item->received_qty,2)); ?></td>
													</tr>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<tr>
														<td colspan="3" class="text-right">Total</td>
														<td colspan=""><?php echo e(isset($approved)?number_format($approved->sum('unit_amount'),2):0); ?></td>
														<td colspan=""><?php echo e(isset($sumOfItemQty)?number_format($sumOfItemQty,0):0); ?></td>
														<td><?php echo e(isset($sumOfReceivedtQty)?number_format($sumOfReceivedtQty,0):0); ?></td>
														<td><?php echo e(isset($sumOfReturntQty)?number_format($sumOfReturntQty,0):0); ?></td>
														<td colspan=""><?php echo e(isset($approved)?number_format($sumOfSubtotal,2):0); ?></td>
													</tr>
													<tr>
														<td colspan="7" class="text-right">(-) Discount</td>
														<td><?= number_format($discountAmount,2)?></td>
													</tr>
													<tr>
														<td colspan="7" class="text-right">(+) Vat</td>
														<td><?php echo e(number_format($vatAmount,2)); ?></td>
													</tr>
													<tr>
														<td colspan="7" class="text-right"><strong>Total Amount</strong></td>
														<td><strong><?php echo e(number_format(($sumOfSubtotal-$discountAmount)+$vatAmount,2)); ?></strong></td>
													</tr>
													<?php else: ?>
													<tr>
														<td colspan="8" class="text-right">No Data Found</td>
													</tr>
													<?php endif; ?>

												</tbody>
											</table>

										</div>
										<div class="form-group">
											<label for="remarks"><strong>Remarks</strong>:</label>

											<span style="font-size:18px !important"><?php echo $quotation->relPurchaseOrder->remarks?$quotation->relPurchaseOrder->remarks:''; ?></span>
										</div>

										<div class="form-group">
											<label for="terms-condition"><strong>Terms & Conditions</strong>:</label>
											<div class="pl-4"><?php echo isset($quotation->relPurchaseOrder->relQuotation->relSuppliers->term_condition) ? $quotation->relPurchaseOrder->relQuotation->relSuppliers->term_condition : ''; ?></div>
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

</html><?php /**PATH D:\xampp\htdocs\procurement.zbridea.com\resources\views/pms/backend/pages/quality/return-approved-item-print-view.blade.php ENDPATH**/ ?>