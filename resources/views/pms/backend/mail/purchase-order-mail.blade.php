@php
$corporateAddress = \App\Models\PmsModels\SupplierAddress::where(['supplier_id' => isset($purchaseOrder->relQuotation->relSuppliers->id) ? $purchaseOrder->relQuotation->relSuppliers->id : 0, 'type' => 'corporate'])->first();
$contactPersonSales = \App\Models\PmsModels\SupplierContactPerson::where(['supplier_id' => isset($purchaseOrder->relQuotation->relSuppliers->id) ? $purchaseOrder->relQuotation->relSuppliers->id : 0, 'type' => 'sales'])->first();
@endphp
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Purchase Order Mail</title>
	<link rel="shortcut icon" href="{{ asset('images/mbm.ico')}}"/>
	<style type="text/css">
		.invoiceBody{
			margin-top:10px;
			background:#eee;
			padding: 20px;
			padding-left: 30px;
		}
		
		th {
			vertical-align: bottom;
			border-bottom: 2px solid #dee2e6;
		}
		tbody {
			display: table-row-group;
			vertical-align: middle;
			border-color: inherit;
		}
		tr {
			display: table-row;
			vertical-align: inherit;
			border-color: inherit;
		}
		table td {
			padding: 0.75rem;
			vertical-align: top;
			border-top: 1px solid #dee2e6;
			border: 1px solid #dee2e6;
		}
		.form-group {
			margin-bottom: 1rem;
		}
		label {
			display: inline-block;
			margin-bottom: 0.5rem;
		}
		strong {
			font-weight: bolder;
		}

		.list-unstyled {padding-left: 0;list-style: none;
		}

		.main-body {
			page-break-after: always;
		}
		
	</style>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
	<div id="app">

		<div style="max-width: 1190px;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;">
			<main class="" style="padding-bottom: 0;">
				<div id="main-body">
					<div class="main-content">
						<div class="main-content-inner">
							<div class="breadcrumbs ace-save-state" id="breadcrumbs">
							</div>
							@if(isset($purchaseOrder))

							<div style="display: flex;flex-wrap: wrap;margin-right: -15px;margin-left: -15px;">

								<?php 
								$TS = number_format($purchaseOrder->relQuotation->relSuppliers->SupplierRatings->sum('total_score'),2);
								$TC = $purchaseOrder->relQuotation->relSuppliers->SupplierRatings->count();

								$totalScore = isset($TS)?$TS:0;
								$totalCount = isset($TC)?$TC:0;
							?>

							<div style="flex: 0 0 100%;max-width: 100%; padding-top:30px" id="print_invoice">

								<div class="panel panel-body">

									<div class="invoiceBody" style="max-width: 100%;">
										<div class="invoice-details" style="margin-top:25px;display: flex;flex-wrap: wrap;margin-right: -15px;margin-left: -15px;">
											<table style="width: 100%;margin-bottom: 1rem;color: #212529;border: 1px solid #dee2e6;border-collapse: collapse;box-sizing: border-box;isplay: table;border-collapse: separate;box-sizing: border-box;text-indent: initial;border-color: grey;">
												<tr>
													<td style="width: 50% !important">
														<strong>Vendor Name:&nbsp;{{isset($purchaseOrder->relQuotation->relSuppliers->name) ? $purchaseOrder->relQuotation->relSuppliers->name : ''}}</strong>
													</td>
													<td style="width: 50% !important;text-align: right !important">
														<img src="data:image/png;base64,{!!DNS1D::getBarcodePNG($purchaseOrder->reference_no, 'C39',1,33)!!}" alt="barcode" style="float: right" />
													</td>
												</tr>
												<tr>
													<td style="width: 50% !important;font-size: 14px !important;">
														Address:&nbsp;
														@if(isset($corporateAddress->id))
														{{ $corporateAddress->road.' '.$corporateAddress->village.', '.$corporateAddress->city.'-'.$corporateAddress->zip.', '.$corporateAddress->country }}
														<br>
														{{ $corporateAddress->adddress }}
														@endif
													</td>
													<td style="width: 50% !important;font-size: 14px !important;text-align:right" class="text-right">
														PO Ref. No:&nbsp;{{ $purchaseOrder->reference_no }}
														<br>
														PO Date:&nbsp;{{ date('jS F Y', strtotime($purchaseOrder->po_date)) }}
														<br>
														Quotation Ref. No:&nbsp;{{ isset($purchaseOrder->relQuotation->id) ? $purchaseOrder->relQuotation->reference_no : '' }}
													</td>
												</tr>
												<tr>
													<td style="width: 50% !important;font-size: 14px !important;">
														Attention:&nbsp;
														@if(isset($contactPersonSales->id))
														{{ $contactPersonSales->name.', '.$contactPersonSales->designation }},
														<br>
														Mobile:&nbsp;{{ $contactPersonSales->mobile }},
														<br> 
														Mail:&nbsp;{{ $contactPersonSales->email }}
														@endif
													</td>
													<td style="width: 50% !important;font-size: 14px !important;text-align:right" class="text-right">
														Delivery Location:&nbsp;
														{{$purchaseOrder->Unit->hr_unit_name}}
														<div>
															{!! $purchaseOrder->Unit->hr_unit_address?$purchaseOrder->Unit->hr_unit_address:'' !!}
														</div>
													</td>
												</tr>
												<tr>
													<td style="width: 50% !important;font-size: 14px !important;font-weight: bold !important">
														Payment Mode:&nbsp;{{ isset($purchaseOrder->relQuotation->relSupplierPaymentTerm->relPaymentTerm->term) ? $purchaseOrder->relQuotation->relSupplierPaymentTerm->relPaymentTerm->term : '' }}
													</td>
													<td style="width: 50% !important;font-size: 14px !important;text-align:right" class="text-right">
														Delivery Contact:&nbsp;{!! isset($purchaseOrder->Unit->hr_unit_telephone) ? $purchaseOrder->Unit->hr_unit_telephone : ''!!}
													</td>
												</tr>
											</table>
											

										</div>
									</div>
									<div style="display: block;width: 100%;overflow-x: auto;">

										<table style="width: 100%;margin-bottom: 1rem;color: #212529;border: 1px solid #dee2e6;border-collapse: collapse;box-sizing: border-box;isplay: table;border-collapse: separate;box-sizing: border-box;text-indent: initial;border-color: grey;">
											<thead style="display: table-header-group;vertical-align: middle;border-color: inherit;">
												<tr style="display: table-row;vertical-align: inherit;">
													<th>SL</th>
													<th>Product</th>
													<th>Unit</th>
													<th>Unit Price</th>
													<th>Qty</th>
													<th>Price</th>
												</tr>
											</thead>
											<tbody>


												@foreach($purchaseOrder->relPurchaseOrderItems as $key=>$item)
												<tr>
													<td>{{$key+1}}</td>
													<td>{{$item->relProduct->name}}</td>
													<td>{{$item->relProduct->productUnit->unit_name}}</td>
													<td style="text-align: right">{{$item->unit_price}}</td>
													<td style="text-align: right">{{number_format($item->qty,0)}}</td>
													<td style="text-align: right">{{number_format($item->sub_total_price,2)}}</td>
												</tr>
												@endforeach

												<tr>
													<td colspan="3" style="text-align: right">Total</td>
													<td style="text-align: right">{{number_format($purchaseOrder->relPurchaseOrderItems->sum('unit_price'),2)}}</td>
													<td style="text-align: right">{{$purchaseOrder->relPurchaseOrderItems->sum('qty')}}</td>
													<td style="text-align: right">{{number_format($purchaseOrder->relPurchaseOrderItems->sum('sub_total_price'),2)}}</td>
												</tr>

												<tr>
													<td colspan="5"  style="text-align: right">(-) Discount</td>
													<td style="text-align: right">{{number_format($purchaseOrder->discount,2)}}</td>
												</tr>
												<tr>
													<td colspan="5"  style="text-align: right">(+) Vat </td>
													<td style="text-align: right">{{number_format($purchaseOrder->vat,2)}}</td>
												</tr>
												<tr>
													<td colspan="5"  style="text-align: right"><strong>Total Amount</strong></td>
													<td style="text-align: right"><strong>{{number_format($purchaseOrder->gross_price,2)}}</strong></td>
												</tr>
											</tbody>
										</table>

									</div>
									
									<div class="form-group">
										<label for="remarks"><strong>Remarks</strong>:</label>

										<span style="font-size:18px !important">{!! $purchaseOrder->remarks?$purchaseOrder->remarks:'' !!}</span>
									</div>

									<div class="form-group">
										<label for="terms-condition"><strong>Terms & Conditions</strong>:</label>
										<div class="pl-4">{!! isset($purchaseOrder->relQuotation->relSuppliers->term_condition) ? $purchaseOrder->relQuotation->relSuppliers->term_condition : '' !!}</div>
									</div>

									{{-- <div class="form-group">

										<p>Yours truly,</p>
										<p style="font-size:14px">MBM Garments Limited.</p>
										<p class="mt-10"></p>

										<span>------------------------</span>
										<p>Authorize Signature</p>

									</div> --}}

									<div class="form-group">
										<small>(Note: This purchase order doesn’t require signature as it is automatically generated from MBM Group’s ERP)</small>
									</div>

								</div>
							</div>
							
							</div>
							@endif

						</div>
					</div>
				</div>
			</main>
		</div>
	</div>
</body>
</html>
