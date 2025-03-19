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
				<li class="active">{{__($title)}}</li>
				<li class="top-nav-btn">
					<a href="javascript:history.back()" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
				</li>
			</ul>
		</div>

		<div class="page-content">
			<div class="">
				<div class="panel panel-info">
					<div class="panel-body">
							
						{!! Form::open(['route' => 'pms.grn.stock.in.store',  'files'=> false, 'id'=>'', 'class' => '']) !!}
						<div class="table-responsive style-scroll">
							<table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="5%">{{__('SL No.')}}</th>
										<th>{{__('Ref No')}}</th>
										<th>{{__('Category')}}</th>
										<th>{{__('Product')}}</th>
										<th>{{__('Unit Price')}}</th>
										<th width="10%">{{__('Qty')}}</th>
										<th>{{__('Total Price')}}</th>
										<th>{{__('Is Stock In')}}</th>
										<th>{{__('Warehouses')}}</th>
									</tr>
								</thead>
								<tbody>
									@if(count($grn_stock_in_lists)>0)
									@foreach($grn_stock_in_lists as $key=> $values)
									<tr id="hideRow{{$values->id}}">
										<td> {{ $key + 1 }}</td>
										@if($key==0)
										<td rowspan="{{$grn_stock_in_lists->count()}}">{{ucfirst($values->relGoodsReceivedItems->relGoodsReceivedNote->reference_no)}}</td>
										@endif
										<td>{{$values->relGoodsReceivedItems->relProduct->category->name}}</td>
										<td>{{$values->relGoodsReceivedItems->relProduct->name}} ({{ getProductAttributes($values->relGoodsReceivedItems->product_id) }})</td>
										
										<td>{{number_format($values->unit_amount,2)}}</td>
										<td><input type="text" name="id[{{$values->id}}]" value="{{$values->received_qty}}" readonly class="form-control rounded"></td>
										<td>{{number_format($values->total_amount,2)}}</td>
										
										<td>{{ucfirst($values->is_grn_complete)}}</td>
										<td class="text-center">
											<div class="input-group input-group-md mb-3 d-">
												<select style="width:200px" name="warehouse_id[{{$values->id}}]" id="warehouse_id{{$values->id}}" class="form-control rounded" required>
													<option value="">Select One</option>
													@foreach($warehouses as $key=> $warehouse)
													<option value="{{ $warehouse->id }}" {{ $warehouses->count() == 1 ? 'selected' : ''  }}>{{ $warehouse->name }}</option>
													@endforeach
												</select>
											</div>
										</td>
									</tr>
									@endforeach
									@endif
								</tbody>
								<input type="hidden" name="goods_received_note_id" value="{{$id}}">
								
							</table>
							<div class="col-12 py-2">
								<button type="submit" class="btn btn-success float-right">Submit</button>
							</div>
						</div>
						{!! Form::close() !!}
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
				<h4 class="modal-title">GRN Details</h4>
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
@endsection
@section('page-script')
<script>
	(function ($) {
		"use script";

		const grnStockInProcess = () => {
            $('.grnStockInProcess').on('click', function () {
                let id= $(this).attr('data-id');
                swal({
                	title: "{{__('Are you sure?')}}",
                	text: 'Would you like to complete GRN process and Stock In?',
                	icon: "warning",
                	dangerMode: true,
                	buttons: {
                		cancel: true,
                		confirm: {
                			text: 'Approved',
                			value: true,
                			visible: true,
                			closeModal: true
                		},
                	},
                }).then((value) => {
                	$.ajax({
                		url: $(this).attr('data-src'),
                		type: 'post',
                		dataType: 'json',
                		data:{_token:'{!! csrf_token() !!}',id:id},
                		success:function (data) {
                			if(data.result == 'success'){
                				$('#hideRow'+id).hide();
                				notify(data.message,'success');
                			}else{
                				notify(data.message,'error');
                			}
                		}
                	});

                });
            });
        };
        grnStockInProcess();

	})(jQuery);
</script>
@endsection