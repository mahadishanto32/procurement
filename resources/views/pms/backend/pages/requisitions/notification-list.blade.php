@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

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
              <li class="active">{{__($title)}} List</li>
              
          </ul>
      </div>

      <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <form action="{{ route('pms.requisition.view.all.notification') }}" method="get" accept-charset="utf-8">
                        <div class="row">

                            <div class="col-md-6 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="search_text">Enter Search Text</label></p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="search_text" id="search_text" class="form-control" placeholder="Search Notification Here..." value="{{ request()->has('search_text') ? request()->get('search_text') : '' }}"/>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <p class="mb-1 font-weight-bold"><label for="searchDeliveredRequisitonBtn"></label></p>
                                <div class="input-group input-group-md">
                                    <button type="submit" class="btn btn-success rounded mt-8"><i class="las la-search"></i>&nbsp;Search</button>
                                </div>
                            </div>

                        </div>  
                    </form>                  
                </div>

                <div class="panel-body">
                    <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" border="0" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{__('Unit')}}</th>
                                <th>{{__('Department')}}</th>
                                <th>Requisition Date</th>
                                <th>Notification Date</th>
                                <th>Requisition RefNo</th>
                                <th>Category</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th class="text-center">Option</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($notification))
                            @foreach($notification as $key => $values)
                            <tr>
                                <td>{{($notification->currentpage()-1) * $notification->perpage() + $key + 1 }}</td>
                                <td>
                                    {{isset($values->relUser->employee->unit->hr_unit_short_name)?$values->relUser->employee->unit->hr_unit_short_name:''}}
                                </td>
                                <td>
                                    {{isset($values->relUser->employee->department->hr_department_name)?$values->relUser->employee->department->hr_department_name:''}}
                                </td>
                                <td>{{ isset($values->relRequisitionItem->requisition->requisition_date)?date('d-m-Y',strtotime($values->relRequisitionItem->requisition->requisition_date)):'' }}</td>

                                <td>{{ date('d-m-Y',strtotime($values->created_at)) }}</td>

                                <td>
                                    @if(isset($values->relRequisitionItem->requisition_id))
                                    <a href="javascript:void(0)"  data-src="{{route('pms.requisition.list.view.show',$values->relRequisitionItem->requisition_id)}}" class="btn btn-link requisition m-1 rounded showRequistionDetails">{{ $values->relRequisitionItem->requisition->reference_no }}</a>
                                    @endif
                                </td>
                                <td>{{ isset($values->relRequisitionItem->product->category->name)?$values->relRequisitionItem->product->category->name:'' }}</td>
                                <td>
                                    @if(isset($values->relRequisitionItem->product->name))
                                    {{ $values->relRequisitionItem->product->name }} ({{ getProductAttributes($values->relRequisitionItem->product->id) }})
                                    @endif
                                </td>
                                <td>{{ isset($values->relRequisitionItem->qty)?$values->relRequisitionItem->qty:''}}</td>
                                <td>{!! $values->messages!!}</td>
                                <td id="type{{$values->id}}">
                                    @if($values->type=='unread')
                                    <span class="btn btn-sm btn-warning">Unread</span>
                                    @else
                                    <span class="btn btn-sm btn-success">Read</span>
                                    @endif
                                </td>
                                <td class="text-center action" id="action{{$values->id}}">
                                    @if($values->type=='unread')
                                    <div class="btn-group">
                                        <button class="btn dropdown-toggle" data-toggle="dropdown">
                                            <span id="statusName{{$values->id}}">
                                                {{ __('Option')}}
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu">
                                         <li><a href="javascript:void(0)" class="markAsRead" data-id="{{$values->id}}" title="Mark As Read"><i class="la la-check"></i> {{ __('Mark As Read')}}</a>
                                         </li>
                                     </ul>
                                 </div>
                                 @else
                                 Already Read
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
                            @if(count($notification)>0)
                            <ul>
                                {{$notification->links()}}
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
@endsection

@section('page-script')
<script>

    $('.markAsRead').on('click', function () {

        let id = $(this).attr('data-id');
        
        $.ajax({
            url: "{{ url('pms/requisition/mark-as-read') }}",
            type: 'POST',
            dataType: 'json',
            data: {_token: "{{ csrf_token() }}", id:id},
        })
        .done(function(response) {
            if(response.result=='success'){
                $('#type'+id).html('<span class="btn btn-sm btn-success">Read</span>');
                $('#action'+id).html('<span class="btn btn-sm btn-success">Read</span>');
                notify(response.message,response.result);
            }
        })
        .fail(function(response){
            notify('Something went wrong!','error');
        });
        return false;
    });
</script>
@endsection
