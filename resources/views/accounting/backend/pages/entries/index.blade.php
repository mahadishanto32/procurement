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
                <div class="col-md-8 pt-0">
                    <div class="row pl-3">
                        <div class="col-md-12 pt-0">
                            @include('accounting.backend.pages.reports.buttons', [
                                'title' => $title,
                                'url' => url('accounting/entries'),
                                'searchHide' => true,
                                'clearHide' => true,
                            ])
                        </div>
                    </div>
                </div>
                <div class="col-md-4 pt-2">
                    @can('entry-create')
                        @if(isset($entryTypes[0]))
                        @foreach($entryTypes as $key => $entryType)
                            <a class="btn btn-sm btn-success pull-right ml-2" href="{{ url('accounting/entries/create?type='.$entryType->label) }}" style="float: right"><i class="las la-plus-circle"></i>&nbsp;{{ $entryType->name }}</a>
                        @endforeach
                        @endif
                    @endcan
                </div>
            </div>
            <div class="panel panel-info mt-2 p-2">
                <form action="{{ url('accounting/entries') }}" method="get" accept-charset="utf-8">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from"><strong>From:</strong></label>
                                        <input type="date" name="from" id="from" value="{{ $from }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to"><strong>To:</strong></label>
                                        <input type="date" name="to" id="to" value="{{ $to }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="entry_type_id"><strong>Entry Type:</strong></label>
                                        <div class="input-group input-group-md mb-3 d-">
                                            <select name="entry_type_id" id="entry_type_id" class="form-control rounded">
                                                <option value="0">All Entry types</option>
                                                @if(isset($entryTypes[0]))
                                                @foreach($entryTypes as $key => $entryType)
                                                 <option value="{{ $entryType->id }}" {{ $entry_type_id == $entryType->id ? 'selected' : '' }}>{{ $entryType->name }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tag_id"><strong>Tag:</strong></label>
                                        <div class="input-group input-group-md mb-3 d-">
                                            <select name="tag_id" id="tag_id" class="form-control rounded">
                                                <option value="0">All Tags</option>
                                                @if(isset($tags[0]))
                                                @foreach($tags as $key => $tag)
                                                 <option value="{{ $tag->id }}" {{ $tag_id == $tag->id ? 'selected' : '' }}>{{ $tag->title }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="chart_of_account_id"><strong>Ledger:</strong></label>
                                        <div class="input-group input-group-md mb-3 d-">
                                            <select name="chart_of_account_id" id="chart_of_account_id" class="form-control rounded">
                                            <option value="0">All Ledgers</option>
                                            {!! chartOfAccountsOptions([], $chart_of_account_id) !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 pt-4">
                            <button type="submit" class="btn btn-success btn-md btn-block mt-2"><i class="la la-search"></i>&nbsp;Search</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="panel panel-info mt-2 p-2 export-table">
                <table class="table table-bordered" cellspacing="0" width="100%" id="dataTable">
                    <thead>
                        <tr>
                           <th style="width: 10%">{{__('Date')}}</th>
                           <th style="width: 10%">{{__('Number')}}</th>
                           <th style="width: 30%">{{__('Ledger')}}</th>
                           <th style="width: 10%">{{__('Type')}}</th>
                           <th style="width: 10%">{{__('Tag')}}</th>
                           <th style="width: 10%">{{__('Debit')}}</th>
                           <th style="width: 10%">{{__('Credit')}}</th>
                           <th style="width: 10%">{{__('Actions')}}</th>
                       </tr>
                   </thead>
                   <tbody>
                    @if(isset($entries[0]))
                    @foreach($entries as $key => $entry)
                    <tr>
                        <td>{{ $entry->date }}</td>
                        <td>{{ $entry->number }}</td>
                        <td>
                            <p>Debit: {{ $entry->items->where('debit_credit', 'D')->pluck('chartOfAccount.code')->implode(', ') }}</p>
                            <p>Credit: {{ $entry->items->where('debit_credit', 'C')->pluck('chartOfAccount.code')->implode(', ') }}</p>
                        </td>
                        <td>{{ $entry->entryType ? $entry->entryType->name : '' }}</td>
                        <td>{{ $entry->tag ? $entry->tag->title : '' }}</td>
                        <td class="text-right">{{ $entry->debit }}</td>
                        <td class="text-right">{{ $entry->credit }}</td>
                        <td class="text-center">
                            @can('entry-view')
                                <a class="btn btn-xs btn-primary" href="{{ url('accounting/entries/'.$entry->id) }}"><i class="lar la-eye"></i></a>
                            @endcan

                            @can('entry-edit')
                                <a class="btn btn-xs btn-primary" href="{{ url('accounting/entries/'.$entry->id.'/edit') }}"><i class="la la-edit"></i></a>
                            @endcan

                            @can('entry-delete')
                                <a class="btn btn-xs btn-danger deleteBtn" data-src="{{ route('accounting.entries.destroy', $entry->id) }}"><i class="la la-trash"></i></a>
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