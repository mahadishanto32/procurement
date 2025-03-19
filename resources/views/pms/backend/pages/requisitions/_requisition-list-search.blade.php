<table class="table table-striped table-bordered table-head datatable-exportable" cellspacing="0" width="100%" id="dataTable" data-table-name="User Requisition">
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
		<tr id="removeRow{{$values->id}}">
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
								<li><a href="javascript:void(0)" title="Click Here To Pending" class="requisitionApprovedBtn" data-id="{{$values->id}}" data-status="0">{{ __('Pending')}}</a>
								</li>
								@endcan
							@endif
							@if($values->status !=1)
								@can('requisition-acknowledge')
								<li><a href="javascript:void(0)" title="Click Here To Acknowledge" class="requisitionApprovedBtn" data-id="{{$values->id}}" data-status="1">{{ __('Acknowledge')}}</a>
								</li>
								@endcan
							@endif
							@can('halt')
							<li><a href="javascript:void(0)" title="Click Here To Halt" class="requisitionApprovedBtn" data-id="{{$values->id}}" data-status="2">{{ __('Halt')}}</a>
							</li>
							@endcan
						@endif

						@can('send-to-rfp')
						<li>
							<a class="sendToPurchaseDepartment" data-src="{{route('pms.store-manage.send.to.purchase.department')}}" data-id="{{$values->id}}"  title="Send To RFP">{{ __('Send To RFP')}}
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
	<ul class="searchPagination">
		{{$requisitionData->links()}}
	</ul>
	@endif
</div>