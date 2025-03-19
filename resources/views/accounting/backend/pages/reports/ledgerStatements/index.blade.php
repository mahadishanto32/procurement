@extends('accounting.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .col-form-label{
        font-size: 14px;
        font-weight: 600;
    }
    .select2-container{
        width: 100% !important;
    }

    .select2-container--default .select2-results__option[aria-disabled=true]{
        color: black !important;
        font-weight:  bold !important;
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
            <div class="panel panel-info mt-2 p-2">
               <div id="accordion">
                  <div class="card">
                    <div class="card-header bg-primary p-0" id="headingOne">
                      <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#filter" aria-expanded="true" aria-controls="filter">
                          <h5 class="text-white"><strong><i class="las la-chevron-circle-right"></i>&nbsp;Filters</strong></h5>
                        </button>
                      </h5>
                    </div>

                    <div id="filter" class="collapse {{ !request()->has('from') ? 'show' : '' }}" aria-labelledby="headingOne" data-parent="#accordion">
                      <div class="card-body">
                        <form action="{{ url('accounting/ledger-statement') }}" method="get" accept-charset="utf-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="chart_of_account_id"><strong>Ledger Account</strong></label>
                                        <div class="input-group input-group-md mb-3 d-">
                                            <select name="chart_of_account_id" id="chart_of_account_id" class="form-control">
                                                {!! chartOfAccountsOptions([], $chart_of_account_id) !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="from"><strong>Start Date</strong></label>
                                        <input type="date" name="from" id="from" value="{{ $from }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="to"><strong>End Date</strong></label>
                                        <input type="date" name="to" id="to" value="{{ $to }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6 ml-3">
                                    @include('accounting.backend.pages.reports.buttons', [
                                        'url' => url('accounting/ledger-statement?chart_of_account_id='.request()->get('chart_of_account_id').'&from='.request()->get('from').'&to='.request()->get('to')),
                                    ])
                                </div>
                            </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            @if(isset($account->id))
            <div class="panel panel-info mt-2 p-2">
                <table style="width: 100% !important" class="export-table">
                    <tbody>
                        <tr>
                            <td colspan="2" class="pt-3 pb-3">
                                <h5>Ledger statement for <strong>[{{ $account->code }}] {{ $account->name }}</strong> from <strong>{{ date('d-M-Y', strtotime($from)) }}</strong> to <strong>{{ date('d-M-Y', strtotime($to)) }}</strong></h5>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%" class="pr-3">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td style="width: 50% !important">Bank or cash account</td>
                                            <td style="width: 50% !important">{{ $account->bank_or_cash == 1 ? 'Yes' : 'No' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50% !important">Notes</td>
                                            <td style="width: 50% !important">{{ $account->notes }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="width: 50%" class="pl-3">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td style="width: 50% !important">Opening balance as on <strong>{{ date('d-M-Y', strtotime($from)) }}</strong></td>
                                            <td style="width: 50% !important">{{ $openingBalance }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50% !important">Closing balance as on <strong>{{ date('d-M-Y', strtotime($to)) }}</strong></td>
                                            <td style="width: 50% !important">{{ $closingBalance }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table class="table table-striped table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                           <th style="width: 10%">{{__('Date')}}</th>
                                           <th style="width: 10%">{{__('Number')}}</th>
                                           <th style="width: 25%">{{__('Ledger')}}</th>
                                           <th style="width: 10%">{{__('Type')}}</th>
                                           <th style="width: 15%" class="text-right">{{__('Debit')}}</th>
                                           <th style="width: 15%" class="text-right">{{__('Credit')}}</th>
                                           <th style="width: 15%" class="text-right">{{__('Balance')}}</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                    <tr>
                                        <td colspan="6">Current opening balance</td>
                                        <td class="text-right">{{ $openingBalance }}</td>
                                    </tr>
                                    @if(isset($entries[0]))
                                    @foreach($entries as $key => $entry)
                                    @php 
                                        $balance = ($openingBalance+($entry->debit-$entry->credit));  
                                    @endphp
                                    <tr>
                                        <td>{{ $entry->date }}</td>
                                        <td>{{ $entry->number }}</td>
                                        <td>
                                            <p>Debit: {{ $entry->items->where('debit_credit', 'D')->pluck('chartOfAccount.code')->implode(', ') }}</p>
                                            <p>Credit: {{ $entry->items->where('debit_credit', 'C')->pluck('chartOfAccount.code')->implode(', ') }}</p>
                                        </td>
                                        <td>{{ $entry->entryType ? $entry->entryType->name : '' }}</td>
                                        <td class="text-right">{{ $entry->debit }}</td>
                                        <td class="text-right">{{ $entry->credit }}</td>
                                        <td class="text-right">{{ $balance }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    <tr>
                                        <td colspan="6">Current closing  balance</td>
                                        <td class="text-right">{{ $closingBalance }}</td>
                                    </tr>
                                   </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection