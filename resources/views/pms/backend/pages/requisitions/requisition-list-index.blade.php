@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
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
				</li>
			</ul><!-- /.breadcrumb -->

		</div>

		<div class="page-content">
			<div class="">
				<div class="panel panel-info">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="from_date">{{ __('From Date') }}:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<input type="text" name="from_date" id="from_date" class="form-control rounded search-datepicker" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old('from_date')?old('from_date'):date("d-m-Y", strtotime(date('Y-m-01'))) }}" readonly>
								</div>
							</div>
							<div class="col-md-3 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="to_date">{{ __('To Date') }}:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<input type="text" name="to_date" id="to_date" class="form-control search-datepicker rounded" aria-label="Large" aria-describedby="inputGroup-sizing-sm" required value="{{ old('to_date')?old('to_date'):date('d-m-Y') }}" readonly>
								</div>
							</div>
							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="requisition_by">{{ __('Requisition By') }}:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<select name="requisition_by" id="requisition_by" class="form-control rounded">
										<option value="{{ null }}">{{ __('Select One') }}</option>
										@foreach($requisitionUserLists as $values)
										<option value="{{ $values->id }}">{{ $values->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="requisition_status">{{ __('Status') }}:</label></p>
								<div class="input-group input-group-md mb-3 d-">
									<select name="requisition_status" id="requisition_status" class="form-control rounded">
										<option value="{{ null }}">{{ __('Select One') }}</option>
										@foreach(statusArrayForHead() as $key=> $values)
										<option value="{{ $key }}">{{ $values }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-2 col-sm-6">
								<p class="mb-1 font-weight-bold"><label for="searchRequisitonBtn"></label></p>
								<div class="input-group input-group-md">
									<a href="javascript:void(0)" class="btn btn-success rounded mt-8" data-src="{{route('pms.requisition.list.view.search')}}" id="searchRequisitonBtn"> <i class="las la-search"></i>Search</a>
								</div>
							</div>

						</div>

						<div id="viewResult">
							<table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" border="0" id="dataTable">
								<thead>
									<tr>
										<th width="5%">{{__('SL No.')}}</th>
										<th>{{__('Unit')}}</th>
										
										<th>{{__('Date')}}</th>
										<th>{{__('Reference No')}}</th>
										<th>{{__('Requisition By')}}</th>
										
										<th class="text-center">{{__('Option')}}</th>
									</tr>
								</thead>
								<tbody>
									@if(count($requisitionData)>0)
									@foreach($requisitionData as $key=> $values)
									<?php 
									$createdDate=date('Y-m-d',strtotime($values->created_at));
									$requisitionDate=date('Y-m-d',strtotime($values->requisition_date));
								?>
								<tr id="removeRow{{$values->id}}" class="{{ ($createdDate==$requisitionDate)?'':'bg-warning color-white' }}">
									<td>{{ ($requisitionData->currentpage()-1) * $requisitionData->perpage() + $key + 1 }}</td>
									<td>
										{{$values->relUsersList->employee->unit->hr_unit_short_name?$values->relUsersList->employee->unit->hr_unit_short_name:''}}
									</td>
									
									<td>{{date('d-m-Y', strtotime($values->requisition_date))}}</td>

									<td><a href="javascript:void(0)" data-src="{{route('pms.requisition.list.view.show',$values->id)}}"  class="btn btn-link showRequistionDetails">{{$values->reference_no}}</a></td>

									<td>{{ $values->relUsersList->name }}</td>
									<td class="text-center action">
										<div class="btn-group">
											<button class="btn dropdown-toggle" data-toggle="dropdown">
												<span id="statusName{{$values->id}}">
													@if($values->status==0)
													{{ __('Pending')}}
													@elseif($values->status==1)
													{{ __('Acknowledge')}}
													@else
													{{ __('Halt')}}
													@endif
												</span>
											</button>
											<ul class="dropdown-menu">

												@if($values->is_send_to_rfp=='no')
												@if($values->status !=0)
												@can('pending')
												<li><a href="javascript:void(0)" title="Click Here To Pending" class="requisitionApprovedBtn" data-id="{{$values->id}}"  data-data="pending" data-status="0">{{ __('Pending')}}</a>
												</li>
												@endcan
												@endif

												@if($values->status !=1)
												<li><a href="{{ route('pms.requisition.requisition.edit',$values->id) }}" title="Click Here To Edit"><i class="la la-edit"></i>{{ __('Edit')}}</a>
												</li>
												@can('requisition-acknowledge')
												<li><a href="javascript:void(0)" title="Click Here To Acknowledge" class="requisitionApprovedBtn" data-id="{{$values->id}}" data-data="acknowledged" data-status="1">{{ __('Acknowledge')}}</a>
												</li>
												@endcan
												@endif

												@can('halt')
												<li><a href="javascript:void(0)" title="Click Here To Halt" class="requisitionApprovedBtn"  data-data="halt" data-id="{{$values->id}}" data-status="2">{{ __('Halt')}}</a>
												</li>
												@endcan
												@endif
												@can('send-to-rfp')
												<li>
													<a class="sendToPurchaseDepartment" data-src="{{route('pms.store-manage.send.to.purchase.department')}}" data-id="{{$values->id}}"  title="Send To procurement ">{{ __('Send To Procurement ')}}
													</a>
												</li>
												@endcan

											</ul>
										</div>

									</td>
								</tr>
								@endforeach
								@endif
							</tbody>

						</table>
						<div class="p-3">
							@if(count($requisitionData)>0)
							{{$requisitionData->links()}}
							@endif
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
</div>


<!--Start Requisition Status Change Modal -->
<div class="modal" id="requisitionHoldModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Halt the Requisition</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<!-- Modal body -->
			<form action="{{route('pms.requisition.halt.status')}}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<label for="admin_remark">Notes:</label>

						<input type="hidden" readonly required name="id" id="requisitionId">
						<textarea class="form-control" required name="admin_remark" rows="3" id="admin_remark" placeholder="Write here...."></textarea>
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
<!--End Requisition Status Change Modal -->
<!--Start Acknowledge Status -->
<div class="modal" id="requisitionAckModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Acknowledge Requisition</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<!-- Modal body -->
			<form action="{{ url('pms/requisition/approved-status') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<label for="admin_remark">Notes (Optional):</label>

						<textarea class="form-control" name="admin_remark" rows="3" id="admin_remark" placeholder="Write here..."></textarea>
						<input type="hidden" readonly required name="id" id="requisitionApprovedId">
						<input type="hidden" readonly required name="status" id="requisitionStatus">
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
<!--End Acknowledge Status -->

@endsection
@section('page-script')
<script>
// $('#dataTable').DataTable();
(function ($) {
	"use script";
//Search 
$('#searchRequisitonBtn').on('click', function () {
	let from_date=$('#from_date').val();
	let to_date=$('#to_date').val();
	let requisition_by=$('#requisition_by').val();
	let requisition_status=$('#requisition_status').val();

	const searchRequisitonPagination = () => {
		let container = document.querySelector('.searchPagination');
		let pageLink = container.querySelectorAll('.page-link');
		Array.from(pageLink).map((item, key) => {
			item.addEventListener('click', (e)=>{
				e.preventDefault();
				let getHref = item.getAttribute('href');
				showPreloader('block');
				$.ajax({
					type: 'post',
					url: getHref,
					dataType: "json",
					data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,requisition_by:requisition_by,requisition_status:requisition_status},
					success:function (data) {
						if(data.result == 'success'){
							showPreloader('none');
							$('#viewResult').html(data.body);
							searchRequisitonPagination();
						}else{
							showPreloader('none');
							notify(data.message,'error');

						}
					}
				});
			})

		});
		approvedRequistion();
		sendToPurchaseDepartment();
		showRequistionDetails();
	};

	if (from_date !='' || to_date !='' || requisition_by || requisition_status) {
		showPreloader('block');
		$.ajax({
			type: 'post',
			url: $(this).attr('data-src'),
			dataType: "json",
			data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,requisition_by:requisition_by,requisition_status:requisition_status},
			success:function (data) {
				if(data.result == 'success'){

					showPreloader('none');
					$('#viewResult').html(data.body);
					searchRequisitonPagination();
				}else{
					showPreloader('none');
					$('#viewResult').html('<div class="col-md-12"><center>No Data Found!!</center></div>');
				}	
			}
		});
		return false;
	}else{
		notify('Please enter data first !!','error');

	}
});
//Approved Reject 
const approvedRequistion = () => {
	$('.requisitionApprovedBtn').on('click', function () {
		let id = $(this).attr('data-id');
		let status = $(this).attr('data-status');

		if (status==1){
			$('#requisitionApprovedId').val(id);
			$('#requisitionStatus').val(status);
			return $('#requisitionAckModal').modal('show');
		}else if (status==2){
			$('#requisitionId').val(id)
			return $('#requisitionHoldModal').modal('show');
		}else if(status==0){
			texStatus='Pending';
			textContent='Do you want to pending this requisition?';
		}

		swal({
			title: "{{__('Are you sure?')}}",
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
					url: "{{ url('pms/requisition/approved-status') }}",
					type: 'POST',
					dataType: 'json',
					data: {_token: "{{ csrf_token() }}", id:id, status:status},
				})
				.done(function(response) {
					if(response.success){
						$('#statusName'+id).html(response.new_text);
						$('#removeRow'+id).hide();
						notify(response.message,'success');
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
	});
};
approvedRequistion();

const capitalize = s => (s && s[0].toUpperCase() + s.slice(1)) || "";

//send to purchase department
const sendToPurchaseDepartment = () => {
	$('.sendToPurchaseDepartment').on('click', function () {

		let requisition_id=$(this).attr('data-id');

		if (requisition_id !='') {
			swal({
				title: "{{__('Are you sure?')}}",
				text: "{{__('Once you send it for RFP, You can not rollback from there.')}}",
				icon: "warning",
				dangerMode: true,
				buttons: {
					cancel: true,
					confirm: {
						text: 'Send To RFP',
						value: true,
						visible: true,
						closeModal: true
					},
				},
			}).then((value) => {
				if(value){
					$.ajax({
						type: 'POST',
						url: $(this).attr('data-src'),
						dataType: "json",
						data:{_token:'{!! csrf_token() !!}',requisition_id:requisition_id},
						success:function (data) {
							if(data.result == 'success'){
								$('#row'+requisition_id).hide();
								notify(data.message,'success');
							}else{
								notify(data.message,data.result);
							}
						}
					});
					return false;
				}
			});

		}else{
			notify('Please Select Requisitoin!!','error');
		}
	});
};

sendToPurchaseDepartment();

const showRequistionDetails = () => {
	$('.showRequistionDetails').on('click', function (e) {
		$.ajax({
			url: e.target.getAttribute('data-src'),
			type: 'get',
			dataType: 'json',
			data: '',
			success: function (response) {
				$('#requisitionDetailModal').find('#tableData').html(response);
				$('#requisitionDetailModal').find('.modal-title').html(`Requisition Details`);
				$('#requisitionDetailModal').modal('show');
			}
		});
	});
};
showRequistionDetails();
})(jQuery);



</script>
@endsection