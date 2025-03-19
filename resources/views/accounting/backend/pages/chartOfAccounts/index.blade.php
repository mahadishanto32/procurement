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
            <div class="row" style="margin-top: -15px">
                <div class="col-md-8">
                    <div class="row pl-3">
                        <div class="col-md-12 pt-0">
                            @include('accounting.backend.pages.reports.buttons', [
                                'title' => $title,
                                'url' => url('accounting/chart-of-accounts'),
                                'searchHide' => true,
                                'clearHide' => true,
                            ])
                        </div>
                    </div>
                </div>
                <div class="col-md-4 pt-3">
                    @can('chart-of-accounts-create')
                    <a class="btn btn-sm btn-success pull-right ml-2" href="{{ url('accounting/chart-of-accounts/create') }}"><i class="la la-plus"></i>&nbsp;New Ledger</a>
                    @endcan

                    @can('account-groups-create')
                    <a class="btn btn-sm btn-primary pull-right" href="{{ url('accounting/account-groups/create') }}"><i class="la la-plus"></i>&nbsp;New Group</a>
                    @endcan
                </div>
            </div>

            <div class="panel panel-info mt-3 export-table">
                <table class="table table-head" cellspacing="0" width="100%" id="dataTable">
                    <thead>
                        <tr>
                           <th style="width: 25%">{{__('Account Code')}}</th>
                           <th style="width: 35%">{{__('Account Name')}}</th>
                           <th class="text-center" style="width: 10%">{{__('Type')}}</th>
                           <th class="text-right" style="width: 20%">{{__('Opening Balance')}}</th>
                           <th class="text-center" style="width: 10%">{{__('Actions')}}</th>
                       </tr>
                   </thead>
                   <tbody>
                    {!! $accountGroups !!}
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
                    var row_class = $(this).attr('data-row-class');
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
                                $('.'+row_class).remove();
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