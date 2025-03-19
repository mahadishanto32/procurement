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
						<form action="{{ route('pms.po.cash.approval.list') }}" method="get" accept-charset="utf-8">
							<div class="row">
								
								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="cash_status">{{ __('Supplier') }}:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<select name="cash_status" id="cash_status" class="form-control rounded">
											@if(stringStatusArray())
											@foreach(stringStatusArray() as $key=>$status)
											<option value="{{$key}}" {{ $cash_status == $key ? 'selected' : '' }}>{{$status}}</option>
											@endforeach
											@endif

										</select>
									</div>
								</div>

								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label></label></p>
									<div class="input-group input-group-md">
										<button class="btn btn-success rounded mt-8" type="submit"> <i class="las la-search"></i>Search</button>
									</div>
								</div>
							</div>
						</form>
						
						<div class="page-content">
							<div class="">
								<div class="panel panel-info">
									<table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" border="0">
										<thead>
											<tr>
												<th width="5%">{{__('SL No.')}}</th>
												<th>{{__('Approved Date')}}</th>
												<th>{{__('Reference No')}}</th>
												<th>{{__('Job Type')}}</th>

												<th>{{__('Quotation Ref No')}}</th>
												<th>{{__('Total Price')}}</th>
												<th>{{__('Discount')}}</th>
												<th>{{__('Vat')}}</th>
												<th>{{__('Gross Price')}}</th>
												<th>{{__('Status')}}</th>
												<th class="text-center">{{__('Option')}}</th>
											</tr>
										</thead>
										<tbody id="viewResult">
											@if(count($purchaseOrder)>0)
											@foreach($purchaseOrder as $key=> $values)
											<tr>
												<td>{{ ($purchaseOrder->currentpage()-1) * $purchaseOrder->perpage() + $key + 1 }}</td>
												<td>{{date('d-m-Y',strtotime($values->po_date))}}</td>
												<td>
													<a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$values->id)}}">{{$values->reference_no}}</a></td>

													<td>{{$values->relQuotation?$values->relQuotation->relSuppliers->name:''}}</td>

													<td>{{$values->relQuotation?$values->relQuotation->reference_no:''}}</td>
													<td>{{$values->total_price}}</td>
													<td>{{$values->discount}}</td>
													<td>{{$values->vat}}</td>
													<td>{{$values->gross_price}}</td>
													<td>
														@if($values->cash_status=='approved')
														<button  class="btn btn-sm btn-primary"><i class="las la-clipboard-check"></i>
															{{ __('Approved') }}
														</button>
														@elseif($values->cash_status=='pending')
														<button  class="btn btn-sm btn-warning"><i class="las la-clipboard-check"></i>
															{{ __('Pending') }}
														</button>
														@else
														<button  class="btn btn-sm btn-danger"><i class="las la-clipboard-check"></i>
															{{ __('Halt') }}
														</button>
														@endif
														<a target="__blank" href="{{route('pms.billing-audit.po.invoice.print',$values->id)}}" class="btn btn-sm btn-warning"><i class="las la-print"></i>
														</a>
													</td>
													<td class="text-center action">
														@can('po-cash-permission')
														@if($values->cash_status!='approved')
														<div class="btn-group">
															<button class="btn dropdown-toggle" data-toggle="dropdown">
																<span id="statusName{{$values->id}}">
																	{{ ucfirst($values->cash_status) }}
																</span>
															</button>
															<ul class="dropdown-menu">
																
																@if(stringStatusArray())
																@foreach(stringStatusArray() as $key=>$status)
																<li><a href="javascript:void(0)" class="updateCashStatus" data-id="{{$values->id}}" data-status="{{$key}}" title="Click Here To {{$status}}"> {{ $status}}</a>
																</li>
																@endforeach
																@endif
															</ul>
														</div>
														@endif
														@endcan
													</td>
												</tr>
												@endforeach
												@endif
											</tbody>
										</table>

										<div class="row">
											<div class="col-md-12">
												<div class="pull-right">
													@if(count($purchaseOrder)>0)
													<ul>
														{{$purchaseOrder->links()}}
													</ul>
													@endif
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

	<div class="modal" id="updateCashModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">Cash Approval</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<!-- Modal body -->
				<form action="{{ route('pms.po.cash.approval.store') }}" method="POST">
					@csrf
					<div class="modal-body">
						<div class="form-group">
							<label for="cash_note">Notes (Optional):</label>

							<textarea class="form-control" name="cash_note" rows="3" id="cash_note" placeholder="Write here..."></textarea>
							<input type="hidden" readonly required name="id" id="purchase_order_id">
							<input type="hidden" readonly required name="cash_status" id="po_cash_status">
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

	@endsection
	@section('page-script')
	<script>
		(function ($) {
			"use script";

			const updateCashStatus = () => {
				$('.updateCashStatus').on('click', function () {
					let id = $(this).attr('data-id');
					let status = $(this).attr('data-status');
					if (status){
						$('#purchase_order_id').val(id);
						$('#po_cash_status').val(status);
						return $('#updateCashModal').modal('show');
					}
				});
			};
			updateCashStatus();
		})(jQuery);
	</script>
	@endsection
