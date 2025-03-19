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
				</ul>
			</div>

			<div class="page-content">
				<div class="">
					<div class="panel panel-info">
						<div class="panel-body">
						<table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" cellspacing="0" width="100%" id="dataTable">
								<thead>
								<tr>
									<th width="5%">{{__('SL No.')}}</th>
									<th>{{__('Req.Date')}}</th>
									<th>{{__('Ref')}}</th>
									<th>{{__('Requisition By')}}</th>
									<th>{{__('Qty')}}</th>
									<th>{{__('Delivery Qty')}}</th>
									<th>{{__('Left Qty')}}</th>
									<th>{{__('Delivery')}}</th>
									<th class="text-center">{{__('Option')}}</th>
								</tr>
								</thead>
								<tbody id="viewResult">
								@if(count($requisitions)>0)
									@foreach($requisitions as $key=> $value)
										<tr id="row{{$value->id}}">
											<td>{{$key+1}}</td>
											<td>{{date('d-m-Y', strtotime($value->requisition_date))}}</td>
											<td><a href="javascript:void(0)" onclick="openModal({{$value->id}})"  class="btn btn-link">{{$value->reference_no}}</a></td>
											<td> {{$value->relUsersList->name}}</td>
											<td> {{$value->requisition_qty}}</td>
											<td> {{$value->total_delivery_qty}}</td>
											<td> {{$value->requisition_qty-$value->total_delivery_qty}}</td>
											<td>
												<a href="{{route('pms.store-manage.requisition-delivered-list',$value->id)}}" data-toggle="tooltip" title="Click here to view details" target="_blank"> Total ({{count($value->relRequisitionDelivery)}})</a>
											</td>
											<td class="text-center action">

												@if($value->delivery_status=='delivered')
												<span>Full Delivered</span>
												@else
												<div class="btn-group">
													<button class="btn dropdown-toggle" data-toggle="dropdown">
														<span id="statusName{{$value->id}}">
															Action
														</span>
													</button>
													<ul class="dropdown-menu">
														@can('confirm-delivery')
														<li><a href="{{route('pms.store-manage.store-requistion.delivery',$value->id)}}" title="Click Here To Confirm Delivery" >{{ __('Confirm Delivery')}}</a>
														</li>
														@endcan
														@can('send-to-rfp')
														@if($value->request_status ==NULL && $value->is_po_generate=='no')
														<li id="hideFromList{{$value->id}}">
															<a class="sendToPurchaseDepartment" data-src="{{route('pms.store-manage.change.action.to.rfp')}}" data-id="{{$value->id}}"  title="Send To Procurement ">{{ __('Send To Procurement ')}}
															</a>
														</li>
														@endif
														@endcan
													</ul>
												</div>
												@endif
											</td>
										</tr>
									@endforeach
								@endif
								</tbody>
							</table>
							<div class="row">
		                        <div class="col-md-12">
		                            <div class="la-1x pull-right">
		                                @if(count($requisitions)>0)
		                                <ul>
		                                    {{$requisitions->links()}}
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

	<div class="modal" id="requisitionDetailModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">Requisition Details</h4>
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
        	
            $('#searchStoreRequisitonBtn').on('click', function () {

                let from_date=$('#from_date').val();
                let to_date=$('#to_date').val();
                let requisition_by=$('#requisition_by').val();
                let requisition_status='1';

                if (from_date !='' || to_date !='' || requisition_by || requisition_status) {
                    $.ajax({
                        type: 'post',
                        url: $(this).attr('data-src'),
                        dataType: "json",
                        data:{_token:'{!! csrf_token() !!}',from_date:from_date,to_date:to_date,requisition_by:requisition_by,requisition_status:requisition_status},
                        success:function (data) {
                            if(data.result == 'success'){
                                $('#viewResult').html(data.body);
                                sendToPurchaseDepartment();
                            }else{
                                //notify(data.message,'error');
                                $('#viewResult').html('<tr><td colspan="6" style="text-align: center;">No Data Found</td></tr>');

                            }
                        }
                    });
                    return false;
                }else{
                    notify('Please enter data first !!','error');

                }
            });

            const sendToPurchaseDepartment = () => {
                $('.sendToPurchaseDepartment').on('click', function () {

                    let requisition_id=$(this).attr('data-id');

                    if (requisition_id !='') {
                        swal({
                            title: "{{__('Are you sure?')}}",
                            text: "{{__('Once you send it for Procurement , You can not rollback from there.')}}",
                            icon: "warning",
                            dangerMode: true,
                            buttons: {
                                cancel: true,
                                confirm: {
                                    text: 'Send To Procurement ',
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
                                            $('#hideFromList'+requisition_id).hide();
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

        })(jQuery);

        function openModal(requisitionId) {
            $('#tableData').load('{{URL::to(Request()->route()->getPrefix()."/store-inventory-compare")}}/'+requisitionId);
            $('#requisitionDetailModal').modal('show');
        }
	</script>
@endsection