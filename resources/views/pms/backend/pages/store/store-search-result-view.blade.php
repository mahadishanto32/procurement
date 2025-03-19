<table class="table table-striped table-bordered table-head datatable-exportable" id="dataTable" data-table-name="Store Search" border="1">
	<thead>
		<tr>
			<th width="5%">{{__('SL No.')}}</th>
			<th>{{__('Unit')}}</th>
			<th>{{__('Department')}}</th>
			<th>{{__('Date')}}</th>
			<th>{{__('Reference No')}}</th>
			
			<th>{{__('Requisition By')}}</th>
			<th>{{__('Approved Qty')}}</th>
			<th class="text-center">{{__('Option')}}</th>
		</tr>
	</thead>
	<tbody id="viewResult">
		@if(count($requistionData)>0)
		@foreach($requistionData as $key=> $values)
		<tr id="row{{$values->id}}">
			<td>{{$key+1}}</td>
			<td>
				{{$values->relUsersList->employee->unit->hr_unit_short_name?$values->relUsersList->employee->unit->hr_unit_short_name:''}}
			</td>
			<td>
				{{$values->relUsersList->employee->department->hr_department_name?$values->relUsersList->employee->department->hr_department_name:''}}
			</td>
			<td>{{date('d-m-Y', strtotime($values->requisition_date))}}</td>
			
			<td><a href="javascript:void(0)" onclick="openModal({{$values->id}})"  class="btn btn-link">{{$values->reference_no}}</a></td>
			
			<td> {{$values->relUsersList->name}}</td>
			<td> {{$values->items->sum('qty')}}</td>
			<td class="text-center action">
				<div class="btn-group">
					<button class="btn dropdown-toggle" data-toggle="dropdown">
						<span id="statusName{{$values->id}}">
							Action
						</span>
					</button>
					<ul class="dropdown-menu">
						@can('confirm-delivery')
						<li><a href="{{route('pms.store-manage.store-requistion.delivery',$values->id)}}" title="Click Here To Confirm Delivery" >{{ __('Confirm Delivery')}}</a>
						</li>
						@endcan
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
<div class="py-2 col-md-12">
	@if(count($requistionData)>0)
	<ul  class="searchPagination">
		{{$requistionData->links()}}
	</ul>
	@endif
</div>