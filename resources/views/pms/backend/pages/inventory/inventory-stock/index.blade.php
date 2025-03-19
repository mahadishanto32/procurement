@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
@endsection
@section('main-content')
@php
use App\Models\PmsModels\InventoryModels\InventorySummary;
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
						<form action="{{ url('pms/inventory/inventory-stock-report') }}" method="post" accept-charset="utf-8" id="inventory-stock-report-form">
						@csrf
							<div class="row">
								@if(isset($attributes[0]))
								@foreach($attributes as $key => $attribute)
								<div class="col-md-2 col-sm-6">
									<p class="mb-0 font-weight-bold"><label for="attribute-{{ $attribute->id  }}">{{ $attribute->name }}:</label></p>
									<div class="input-group input-group-md mb-3 d-">
										<select name="attributesData[{{ $attribute->id }}]" id="attribute-{{ $attribute->id  }}" class="form-control rounded">
											<option value="0">All {{ $attribute->name }}</option>
											@foreach($attribute->options as $option)
											<option value="{{ $option->id }}">{{ $option->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								@endforeach
								@endif
							</div>
							<div class="row mb-3">
								<div class="col-md-2 pr-1">
									<button type="submit" class="btn btn-sm btn-success btn-block"><i class="las la-search"></i>&nbsp;Search</button>
								</div>
								<div class="col-md-2 pl-1">
									<a class="btn btn-sm btn-danger btn-block" onclick="Reset()"> <i class="las la-times"></i>&nbsp;Reset</a>
								</div>
							</div>
						</form>

						<div class="table-responsive style-scroll">
							<table class="table table-striped table-bordered miw-500 dac_table" cellspacing="0" width="100%" id="dataTable">
								<thead>
									<tr>
										<th style="width: 5%">{{__('SL')}}</th>
										<th style="width: 15%">{{__('Category')}}</th>
										<th style="width: 30%">{{__('Product')}}</th>
										<th style="width: 40%">{{__('Attributes')}}</th>
										<th style="width: 15%">{{__('Stock')}}</th>
									</tr>
								</thead>
								<tbody id="stocks">
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="stock-modal" tabindex="-1" role="dialog" aria-labelledby="stock-modal" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-lg">
      <div class="modal-body">
        
      </div>
      <div class="modal-footer p-0">
        <button type="button" class="btn btn-secondary btn-sm mr-3" data-dismiss="modal"><i class="la la-times"></i>&nbsp;Close</button>
      </div>
    </div>
  </div>
</div>

@endsection
@section('page-script')
<script>
	$(document).ready(function() {
		var form = $('#inventory-stock-report-form');
		var button = form.find('button');
		inventoryReport(form, button);

		form.submit(function(event) {
			event.preventDefault();
			inventoryReport(form, button);
		});
	});

	function inventoryReport(form, button) {
		button.attr('disabled', true).html('<i class="las la-spinner la-spin"></i>&nbsp;Please wait...');

		$('#stocks').html('<tr><td colspan="5" class="text-center"><h4 class="text-center"><i class="las la-spinner la-spin"></i>&nbsp;Please wait...</h4></td></tr>');

		$.ajax({
			url: form.attr('action'),
			type: form.attr('method'),
			data: form.serializeArray(),
		})
		.done(function(response) {
			button.attr('disabled', false).html('<i class="las la-search"></i>&nbsp;Search');
			$('#stocks').html(response);
		})
		.fail(function(response) {
			button.attr('disabled', false).html('<i class="las la-search"></i>&nbsp;Search');
			$('#stocks').html('<tr><td colspan="5" class="text-center"><h4 class="text-center text-danger">Something went Wrong!</h4></td></tr>');
		});
	}

	function showWarehouseStocks(product_id) {
		var modal = $('#stock-modal');
		$('.modal-body').html('<h4 class="text-center"><i class="las la-spinner la-spin"></i>&nbsp;Please wait...</h4>');
		modal.modal('toggle');

		$.ajax({
			url: "{{ route('pms.inventory.inventory-stock-report.update', 0) }}?product_id="+product_id,
			type: 'PUT',
			data: $('#inventory-stock-report-form').serializeArray(),
		})
		.done(function(response) {
			$('.modal-body').html(response);
		})
		.fail(function(response) {
			modal.modal('toggle');
		});
	}

	function Reset() {
		$('#inventory-stock-report-form').trigger("reset");
		$('.form-control').select2();
		$('#inventory-stock-report-form').submit();
	}
</script>
@endsection