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
            <div class="row">
                <div class="col-md-12">
                    @can('company-create')
                    <a class="btn btn-sm btn-success pull-right ml-2" href="{{ url('accounting/companies/create') }}" style="float: right"><i class="la la-plus"></i>&nbsp;New Company</a>
                    @endcan
                </div>
            </div>
            <div class="panel panel-info mt-2 p-2">
                <table class="table table-bordered" cellspacing="0" width="100%" id="dataTable">
                    <thead>
                        <tr>
                           <th style="width: 5%">{{__('Code')}}</th>
                           <th style="width: 10%">{{__('Organization')}}</th>
                           <th style="width: 8%">{{__('Owner')}}</th>
                           <th style="width: 10%">{{__('Phone')}}</th>
                           <th style="width: 15%">{{__('Email')}}</th>
                           <th style="width: 20%">{{__('Address')}}</th>
                           <th style="width: 8%">{{__('logo')}}</th>
                           <th style="width: 12%">{{__('Banner')}}</th>
                           <th style="width: 12%">{{__('Actions')}}</th>
                       </tr>
                   </thead>
                   <tbody>
                    @if(isset($companies[0]))
                    @foreach($companies as $key => $company)
                    <tr>
                        <td>{{ $company->code }}</td>
                        <td>{{ $company->name }}</td>
                        <td>{{ $company->owner_name }}</td>
                        <td>{{ $company->phone }}</td>
                        <td>{{ $company->email }}</td>
                        <td>{{ $company->address }}</td>
                        <td class="text-center">
                            @if(!empty($company->logo))
                                <img src="{{ asset($company->logo) }}" style="max-height: 25px">
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!empty($company->banner))
                                <img src="{{ asset($company->banner) }}" style="max-height: 50px">
                            @endif
                        </td>
                        <td class="text-center">
                            @can('company-profile')
                                <a class="btn btn-xs btn-success" href="{{ url('accounting/companies/'.$company->id) }}"><i class="lar la-address-card"></i></a>
                            @endcan

                            @can('company-edit')
                                <a class="btn btn-xs btn-primary" href="{{ url('accounting/companies/'.$company->id.'/edit') }}"><i class="la la-edit"></i></a>
                            @endcan

                            @can('company-delete')
                            <a class="btn btn-xs btn-danger deleteBtn" data-src="{{ route('accounting.companies.destroy', $company->id) }}"><i class="la la-trash"></i></a>
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