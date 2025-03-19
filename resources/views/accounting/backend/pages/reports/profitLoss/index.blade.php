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
                        <form action="{{ url('accounting/profit-loss') }}" method="get" accept-charset="utf-8">
                            <div class="row">
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
                                <div class="col-md-6 pt-4">
                                    @include('accounting.backend.pages.reports.buttons', [
                                        'url' => url('accounting/profit-loss?from='.request()->get('from').'&to='.request()->get('to')),
                                        'title' => $title
                                    ])
                                </div>
                            </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
            </div>

            @if(request()->has('from'))
            <div class="panel panel-info mt-2 p-2">
                <div class="row">
                    <table style="width: 100%" class="export-table">
                        <tbody>
                            <tr>
                                <td style="width: 50%;padding: 15px;vertical-align:top !important">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 75%">
                                                    <h5><strong>{{ isset($expense->id) ? $expense->name : '' }}</strong></h5>
                                                </th>
                                                <th style="width: 25%" class="text-right">
                                                    <h5><strong>Amount</strong></h5>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {!! $expenses !!}
                                        </tbody>
                                    </table>
                                </td>
                                <td style="width: 50%;padding: 15px;vertical-align:top !important">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 75%">
                                                    <h5><strong>{{ isset($income->id) ? $income->name : '' }}</strong></h5>
                                                </th>
                                                <th style="width: 25%" class="text-right">
                                                    <h5><strong>Amount</strong></h5>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {!! $incomes !!}
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection