@extends('accounting.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .col-form-label{
        font-size: 14px;
        font-weight: 600;
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
                <li><a href="#">PMS</a></li>
                <li class="active">Accounts</li>
                <li class="active">{{__($title)}}</li>
            </ul>
        </div>

        <div class="page-content">
            <div class="panel panel-info mt-2 p-3" style="padding-bottom: 0 !important;">
                <form action="{{ url('accounting/cost-centres') }}" method="get" accept-charset="utf-8">
                    <div class="row mb-0">
                        <div class="col-md-4">
                            <div class="input-group input-group-md mb-3 d-">
                                <select name="company_id" id="company_id" class="form-control rounded">
                                    @foreach($companies as $key => $company)
                                    <option value="{{ $company->id }}" {{ request()->get('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-md btn-success"><i class="la la-search"></i>&nbsp;Search</button>
                            <a href="{{ url('accounting/cost-centres') }}" class="btn btn-md btn-danger"><i class="la la-times"></i>&nbsp;Clear</a>
                        </div>
                        <div class="col-md-4 text-right">
                            @can('cost-centre-create')
                                <a class="btn btn-sm btn-success pull-right" href="{{ url('accounting/cost-centres/create') }}" style="float: right"><i class="la la-plus"></i>&nbsp;New Cost Centre</a>
                            @endcan
                        </div>
                    </div>
                </form>
            </div>
            <div class="panel panel-info mt-2 p-2">
                <table class="table table-bordered" cellspacing="0" width="100%" id="dataTable">
                    <thead>
                        <tr>
                           <th style="width: 8%">{{__('Company')}}</th>
                           <th style="width: 7%">{{__('Code')}}</th>
                           <th style="width: 10%">{{__('Cost Centre')}}</th>
                           <th style="width: 8%">{{__('Phone')}}</th>
                           <th style="width: 10%">{{__('Email')}}</th>
                           <th style="width: 20%">{{__('Address')}}</th>
                           <th style="width: 10%">{{__('logo')}}</th>
                           <th style="width: 15%">{{__('Banner')}}</th>
                           <th style="width: 12%">{{__('Actions')}}</th>
                       </tr>
                   </thead>
                   <tbody>
                    @if(isset($costCentres[0]))
                    @foreach($costCentres as $key => $costCentre)
                    <tr>
                        <td>[{{ $costCentre->company->code }}] {{ $costCentre->company->name }}</td>
                        <td>{{ $costCentre->code }}</td>
                        <td>{{ $costCentre->name }}</td>
                        <td>{{ $costCentre->phone }}</td>
                        <td>{{ $costCentre->email }}</td>
                        <td>{{ $costCentre->address }}</td>
                        <td class="text-center">
                            @if(!empty($costCentre->logo))
                                <img src="{{ asset($costCentre->logo) }}" style="max-height: 25px">
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!empty($costCentre->banner))
                                <img src="{{ asset($costCentre->banner) }}" style="max-height: 50px">
                            @endif
                        </td>
                        <td class="text-center">
                            @can('cost-centre-profile')
                                <a class="btn btn-xs btn-success" href="{{ url('accounting/cost-centres/'.$costCentre->id) }}"><i class="lar la-address-card"></i></a>
                            @endcan

                            @can('cost-centre-edit')
                                <a class="btn btn-xs btn-primary" href="{{ url('accounting/cost-centres/'.$costCentre->id.'/edit') }}"><i class="la la-edit"></i></a>
                            @endcan

                            @can('cost-centre-delete')
                            <a class="btn btn-xs btn-danger deleteBtn" data-src="{{ route('accounting.cost-centres.destroy', $costCentre->id) }}"><i class="la la-trash"></i></a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                    @endif
                   </tbody>
                </table>
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
                if(value)form.reset();
            });
        };

        $('.deleteBtn').on('click', function () {
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
                    var button = $(this);
                    $.ajax({
                        type: 'DELETE',
                        url: $(this).attr('data-src'),
                        dataType: 'json',
                        success:function (response) {
                            if(response.success){
                                swal({
                                    icon: 'success',
                                    text: response.message,
                                    button: false
                                });
                                setTimeout(()=>{
                                    swal.close();
                                }, 1500);
                                button.parent().parent().remove();
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
</script>
@endsection