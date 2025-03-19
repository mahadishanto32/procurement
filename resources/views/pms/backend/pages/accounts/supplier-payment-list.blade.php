@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .list-unstyled .ratings {
        display: none;
    }
</style>
@endsection
@section('main-content')

<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="{{  route('pms.dashboard') }}">{{ __('Home') }}</a>
				</li>
				<li>
					<a href="#">PMS</a>
				</li>
				<li>
					<a href="#">Accounts</a>
				</li>
				<li class="active">{{__($title)}}</li>
				<li class="top-nav-btn">
				</li>
			</ul>
		</div>

		<div class="page-content">
			<div class="">
				<div class="panel panel-info">
					<div class="panel-body">
						<form action="{{ url('pms/accounts/supplier-payments') }}" method="get" accept-charset="utf-8">
							<div class="row">
								
								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="supplier_id">{{ __('Supplier') }}:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<select name="supplier_id" id="supplier_id" class="form-control rounded">
											<option value="{{ null }}">{{ __('Select One') }}</option>
											@if(isset($suppliers[0]))
											@foreach($suppliers as $key => $supplier)
											<option value="{{ $supplier->id }}" {{ $supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>

								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="searchRequisitonBtn"></label></p>
									<div class="input-group input-group-md">
										<button class="btn btn-success rounded mt-8" type="submit"> <i class="las la-search"></i>Search</button>
									</div>
								</div>
							</div>
						</form>
						<form action="{{ route('pms.accounts.supplier.payment.store') }}" method="post" accept-charset="utf-8">
						@csrf
							<table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="8%">{{__('SL No.')}}</th>
										<th>{{__('Supplier Name')}}</th>
										<th>{{__('PO Ref No')}}</th>
										<th>{{__('PO Amount')}}</th>
										
										<th>{{__('GRN Amount')}}</th>
										<th>{{__('Bill Amount')}}</th>
										<th>{{__('Paid Amount')}}</th>
										<th>{{__('Due Amount')}}</th>
										<th>{{__('Pay Amount')}}</th>
									</tr>
								</thead>
								<tbody>
									@if(isset($purchase_order))
									@foreach($purchase_order as $pokey=> $values)
									@if($values->relSupplierPayments->count() > 0)
									@foreach($values->relSupplierPayments as $pkey => $payment)
										<tr id="removeRow{{$values->id}}">
											@if($pkey == 0)
											<td rowspan="{{ $values->relSupplierPayments->count() }}">
												{{ $key + 1 }}
											</td>
											
											<td rowspan="{{ $values->relSupplierPayments->count() }}">{{ucfirst($values->relQuotation->relSuppliers->name)}}</td>
											<td rowspan="{{ $values->relSupplierPayments->count() }}">
												<a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$values->id)}}">{{$values->reference_no}}</a>
											</td>
											<td rowspan="{{ $values->relSupplierPayments->count() }}" class="text-right">
												{{number_format($values->gross_price,2)}}
											</td>
											@endif

											@php
												$grn_amount = \App\Models\PmsModels\Grn\GoodsReceivedNote::whereHas('billingChallan.relPurchaseOrderAttachment', function($query) use($values, $payment){
													return $query->where([
														'purchase_order_id' => $values->id,
														'goods_received_note_id' => $payment->goods_received_note_id,
														'bill_type' => $payment->bill_type,
													]);
												})->sum('gross_price');
											@endphp
											
											<td class="text-right" width="10%">
												<input type="text" class="form-control text-right rounded grn-amounts" onkeypress="return isNumberKey(event)"  value="{{number_format($grn_amount,2)}}" readonly>
											</td>

											<td class="text-right" width="10%">
												<input type="text" class="form-control text-right rounded bill-amounts" onkeypress="return isNumberKey(event)"  value="{{number_format($payment->bill_amount,2)}}" readonly>
											</td>
											
											<td class="text-right" width="10%">
												<input type="text" class="form-control text-right rounded paid-amounts" onkeypress="return isNumberKey(event)"  value="{{number_format($payment->pay_amount,2)}}" readonly>
											</td>
											<td class="text-right" width="10%">
												<input type="text" class="form-control text-right rounded due-amounts" @if($payment->bill_amount-$payment->pay_amount < 0) name="due_amount[{{ $payment->id }}]" @endif onkeypress="return isNumberKey(event)"  value="{{number_format(($payment->bill_amount-$payment->pay_amount > 0 ? $payment->bill_amount-$payment->pay_amount : 0),2)}}" readonly>
											</td>
											<td class="text-right" width="10%">
												@if($payment->bill_amount-$payment->pay_amount > 0)
													<input type="number" step="0.01" min="0" max="{{ $payment->bill_amount-$payment->pay_amount }}" class="form-control text-right pay-amounts rounded" name="pay_amount[{{ $payment->id }}]"  onkeypress="return isNumberKey(event)" onkeyup="calculateTotal()" onchange="calculateTotal()" value="{{ $payment->bill_amount-$payment->pay_amount }}">
												@endif
											</td>
										</tr>
									@endforeach
									@endif
									
									@endforeach
									@endif
									<tr>
										<td colspan="4" class="text-right"><strong>Total:</strong></td>
										<td class="text-right" id="grn-amounts"></td>
										<td class="text-right" id="bill-amounts"></td>
										<td class="text-right" id="paid-amounts"></td>
										<td class="text-right" id="due-amounts"></td>
										<td class="text-right" id="pay-amounts"></td>
									</tr>
									<tr>
										<td colspan="9" class="text-right">
											<button class="btn btn-success rounded mt-8 mb-2" type="submit"> <i class="las la-check"></i>&nbsp;Submit Payments</button>
										</td>
									</tr>
								</tbody>
							</table>
						</form>
						<div class="p-3">
							@if(count($purchase_order)>0)
							{{$purchase_order->links()}}
							@endif
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('page-script')
<script>
	(function ($) {
		"use script";

		$('.page-link').click(function(event) {
			event.preventDefault();

			window.open($(this).attr('href')+"&supplier_id={{ $supplier_id }}", "_parent");
		});

		const isNumberKey =(evt) => {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
            {
                return false;
            }
            return true;
        };

	})(jQuery);
</script>

<script type="text/javascript">
	calculateTotal();
	function calculateTotal(){
    	var grn_amount = 0;
    	$.each($('.grn-amounts'), function(index, val) {
    		grn_amount += parseFloat($(this).val().replace(',',''));
    	});

    	var bill_amount = 0;
    	$.each($('.bill-amounts'), function(index, val) {
    		bill_amount += parseFloat($(this).val().replace(',',''));
    	});

    	var paid_amount = 0;
    	$.each($('.paid-amounts'), function(index, val) {
    		paid_amount += parseFloat($(this).val().replace(',',''));
    	});

    	var due_amount = 0;
    	$.each($('.due-amounts'), function(index, val) {
    		due_amount += parseFloat($(this).val().replace(',',''));
    	});

    	var pay_amount = 0;
    	$.each($('.pay-amounts'), function(index, val) {
    		pay_amount += parseFloat($(this).val().replace(',','') != "" ? $(this).val().replace(',','') : 0);
    	});

    	$('#grn-amounts').html('<strong>'+(grn_amount.toFixed(2))+'&nbsp;&nbsp;&nbsp;&nbsp;</strong>');
    	$('#bill-amounts').html('<strong>'+(bill_amount.toFixed(2))+'&nbsp;&nbsp;&nbsp;&nbsp;</strong>');
    	$('#paid-amounts').html('<strong>'+(paid_amount.toFixed(2))+'&nbsp;&nbsp;&nbsp;&nbsp;</strong>');
    	$('#due-amounts').html('<strong>'+(due_amount.toFixed(2))+'&nbsp;&nbsp;&nbsp;&nbsp;</strong>');
    	$('#pay-amounts').html('<strong>'+(pay_amount.toFixed(2))+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>');
    }
</script>
@endsection