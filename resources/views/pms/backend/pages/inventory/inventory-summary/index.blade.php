@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
@endsection
@section('main-content')
@php
use App\Models\PmsModels\InventoryModels\InventoryDetails;
@endphp
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

				</li>
			</ul><!-- /.breadcrumb -->
		</div>

		<div class="page-content">
			<div class="">
				<div class="panel panel-info">
					<div class="panel-body">
						<form action="{{ url('pms/inventory/inventory-summary') }}" method="get" accept-charset="utf-8">
							<div class="row">
								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="category_id">{{ __('Category') }}:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<select name="category_id" id="category_id" onchange="getSubCategory()" class="form-control rounded">
											<option value="0">{{ __('Select One') }}</option>
											@foreach($categories as $values)
											<option value="{{ $values->id }}" {{$category_id==$values->id ? 'selected' : ''}}>{{ $values->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="sub_category_id">{{ __('Sub Category') }}:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<select name="sub_category_id" id="sub_category_id" onchange="getProduct()" class="form-control rounded">

										</select>
									</div>
								</div>
								<div class="col-md-3">
									<p class="mb-1 font-weight-bold"><label for="product_id">{{ __('Product') }}:</label></p>
									<div class="input-group input-group-md mb-3">
										<select class="form-control rounded" name="product_id" id="product_id">

										</select>
									</div>
								</div>

								<div class="col-md-3 col-sm-6">
									<p class="mb-1 font-weight-bold"><label for="searchRequisitonBtn"></label></p>
									<button type="submit" class="btn btn-sm btn-success mt-8"> <i class="las la-search"></i>Search</button>
									<a class="btn btn-sm btn-danger mt-8" href="{{ url('pms/inventory/inventory-summary') }}"> <i class="las la-times"></i>Reset</a>
								</div>
							</div>
						</form>

						<div class="table-responsive style-scroll">
							<table class="table table-striped table-bordered miw-500 dac_table datatable-exportable" data-table-name="{{ $title }}" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th width="5%">{{__('SL No.')}}</th>
										<th>{{__('Category')}}</th>
										<th>{{__('Product')}}</th>
										{{-- <th>{{__('Unit Price')}}</th> --}}
										<th>{{__('Qty')}}</th>
										{{-- <th>{{__('Total Price')}}</th> --}}
									</tr>
								</thead>
								<tbody>
									@if(isset($inventories[0]))
									@foreach($inventories as $key=> $values)
									@php
									$summariesQty=InventoryDetails::where('product_id',$values->product_id)->where('hr_unit_id',auth()->user()->employee->as_unit_id)->sum('qty');
									@endphp
									<tr>
										<td>{{$key+1}}</td>
										<td>{{$values->relCategory->name}}</td>
										<td>
											<a href="javascript:void(0)" onclick="openModal({{$values->relProduct->id}})"  class="btn btn-link">
												{{$values->relProduct->name}} ({{ getProductAttributes($values->product_id) }})
											</a>
										</td>
										{{-- <td>{{number_format($values->unit_price,2)}}</td> --}}
										<td>{{$summariesQty}}</td>
										{{-- <td>{{number_format($values->unit_price*$values->qty,2)}}</td> --}}
									</tr>
									@endforeach
									@endif
								</tbody>
							</table>

							<div class="row">
			                    <div class="col-md-12">
			                        <div class="la-1x pull-right">
			                            @if(count($inventories)>0)
			                            <ul>
			                                {{$inventories->links()}}
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
@endsection
@section('page-script')
<script>
	getSubCategory();
	function getSubCategory(){
		var category_id = $('#category_id').val();
		var sub_category_id = "{{$sub_category_id}}";
		$('#sub_category_id').html('<option value="0">Select One</option>');

		$.ajax({
			url: "{{ url('pms/inventory/inventory-summary') }}/"+category_id+"/get-products?sub-categories",
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
		var product_id = "{{$product_id}}";
		$('#product_id').html('<option value="0">Select One</option>');

		$.ajax({
			url: "{{ url('pms/inventory/inventory-summary') }}/"+category_id+"/get-products?sub_category_id="+sub_category_id,
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
		$('#tableData').load('{{URL::to('pms/inventory/warehouse-wise-product-inventory-details')}}/'+product_id);
		$('#warehouseDetailModal').modal('show');
	}
</script>
@endsection