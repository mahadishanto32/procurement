@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')

@endsection

@section('main-content')
<!-- WRAPPER CONTENT ----------------------------------------------------------------------------->
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
              <li class="top-nav-btn">
                <a href="{{route('pms.requisition.requisition.create')}}" class="btn btn-sm btn-primary text-white" data-toggle="tooltip" title="Add New Requisition" id="addProductBtn"> <i class="las la-plus"></i>Add</a>
            </li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="">
            <div class="panel panel-info">
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" border="0" id="dataTable">
                        <thead>
                            <tr>
                                <th width="2%">{{__('SL No.')}}</th>
                                <th>{{__('Unit')}}</th>
                                <th>{{__('Department')}}</th>
                                <th>{{__('Date')}}</th>
                                <th>{{__('Ref: No')}}</th>
                                <th>{{__('Requisition By')}}</th>
                                <th>{{__('Qty')}}</th>
                                <th>{{__('Status')}}</th>
                                <th class="text-center">{{__('Option')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisitions as $key => $requisition)
                            <tr>
                                <td width="5%">{{($requisitions->currentpage()-1) * $requisitions->perpage() + $key + 1 }}</td>
                                <td>
                                    {{$requisition->relUsersList->employee->unit->hr_unit_short_name?$requisition->relUsersList->employee->unit->hr_unit_short_name:''}}
                                </td>
                                <td>
                                    {{$requisition->relUsersList->employee->department->hr_department_name?$requisition->relUsersList->employee->department->hr_department_name:''}}
                                </td>
                                <td>
                                    {{ date('d-m-Y',strtotime($requisition->requisition_date)) }}
                                </td>

                                <td><a href="javascript:void(0)" data-src="{{route('pms.requisition.list.view.show',$requisition->id)}}" class="btn btn-link requisition m-1 rounded showRequistionDetails">{{ $requisition->reference_no }}</a></td>
                                <td>{{ $requisition->relUsersList->name }}</td>
                                <td>{{$requisition->items->sum('qty')}}</td>
                                <td id="status{{$requisition->id}}">
                                    @if($requisition->status==0)
                                        <span class="btn btn-sm btn-warning">Pending</span>
                                    @elseif($requisition->status==1)
                                        <span class="btn btn-sm btn-success">Approved</span>
                                    @elseif($requisition->status==2)
                                        <span class="btn btn-sm btn-danger">Halt</span>
                                    @elseif($requisition->status==3)
                                        <span class="btn btn-sm btn-warning">Draft</span>
                                    @endif
                                </td>

                                <td class="text-center action">
                                    <div class="btn-group">
                                        <button class="btn dropdown-toggle" data-toggle="dropdown">
                                            <span id="statusName{{$requisition->id}}">
                                                {{ __('Option')}}
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($requisition->status==0 || $requisition->status==2 || $requisition->status==3)
                                            <li><a href="{{ route('pms.requisition.requisition.edit',$requisition->id) }}" title="Click Here To Edit"><i class="la la-edit"></i>{{ __('Edit')}}</a>
                                            </li>
                                            @if($requisition->status==3)
                                            <li><a href="javascript:void(0)" class="sendRequisition" data-id="{{$requisition->id}}" data-status="0" title="Click Here To Send"><i class="la la-paper-plane"></i> {{ __('Send')}}</a>
                                            </li>
                                            @endif
                                            @endif
                                            <li><a href="javascript:void(0)" title="Tracking Requisition" class="trackingRequistionStatus" data-id="{{$requisition->id}}"><i class="la la-map"></i> {{ __('Track Your Requisition')}}</a>
                                            </li>
                                            @if($requisition->status != 1 )
                                            <li>
                                                <a href="javascript:void(0)" data-role="delete" data-src="{{ route('pms.requisition.requisition.destroy', $requisition->id) }}" class="text-danger deleteBtn"><i class="las la-trash"></i>&nbsp;Delete Requisition</a>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
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
@endsection

@section('page-script')
<script>
    (function ($) {
        "use script";

        const showAlert = (status, error) => {
            swal({
                icon: status,
                text: error,
                dangerMode: true,
                buttons: {
                    cancel: false,
                    confirm: {
                        text: "OK",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                // if(value) form.reset();
            });
        };

        $('.deleteBtn').on('click', function () {
            var element = $(this);
            swal({
                title: "{{__('Are you sure?')}}",
                text: "{{__('Once you delete, You can not recover this data and related files.')}}",
                icon: "warning",
                dangerMode: true,
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Delete",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                if(value){
                    $.ajax({
                        type: 'DELETE',
                        url: element.attr('data-src'),
                        success:function (response) {
                            if(response.success){
                                element.parent().parent().parent().parent().parent().remove();
                                swal({
                                    icon: 'success',
                                    text: 'Data deleted successfully',
                                    button: false
                                });
                                setTimeout(()=>{
                                    swal.close();
                                }, 1500);
                            }else{
                                showAlert('error', response.message);
                                return;
                            }
                        },
                    });
                }
            });
        })
    })(jQuery)

    $('.trackingRequistionStatus').on('click', function () {
        let id = $(this).attr('data-id');
        $.ajax({
            url: "{{ url('pms/requisition/tracking-show') }}",
            type: 'POST',
            dataType: 'json',
            data: {_token: "{{ csrf_token() }}", id:id},
        })
        .done(function(response) {
            if(response.result=='success'){
                $('#requisitionDetailModal').find('.modal-title').html(`Requisition Tracking`);
                $('#requisitionDetailModal').find('#tableData').html(response.body);
                $('#requisitionDetailModal').modal('show');
            }else{
                notify(response.message,response.result);
            }
        })
        .fail(function(response){
            notify('Something went wrong!','error');
        });
        return false;
    });

    $('.sendRequisition').on('click', function () {
        let sendButton=$(this).parent('li');
        let id = $(this).attr('data-id');
        let status = $(this).attr('data-status');

        let texStatus='Send';
        let textContent='Would you like to send this requisition to your department head?';

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
                        $('#status'+id).html('<span class="btn btn-sm btn-warning">'+response.new_text+'</span>');
                        notify(response.message,'success')
                        sendButton.remove();
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
</script>
@endsection
