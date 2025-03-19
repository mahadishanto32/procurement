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
                <div class="row">
                    <div class="col-md-8">
                        <h5>
                            Trial Balance from <strong>{{ date('d-M-Y', strtotime($fiscalYear->start)) }}</strong> to <strong>{{ date('d-M-Y', strtotime($fiscalYear->end)) }}</strong>
                        </h5>
                    </div>
                    <div class="col-md-4 text-right">
                        @include('accounting.backend.pages.reports.buttons', [
                            'title' => "Trial Balance from ".date('d-M-Y', strtotime($fiscalYear->start))." to ".date('d-M-Y', strtotime($fiscalYear->end)),
                            'url' => url('accounting/trial-balance'),
                            'searchHide' => true,
                            'clearHide' => true,
                        ])
                    </div>
                </div>
                <div class="row export-table">
                    <div class="col-md-12">
                        <table class="table table-hover mt-4">
                           <thead>
                               <tr>
                                   <th style="width: 40%">Ledger</th>
                                   <th style="width: 15%" class="text-right">Opening Balance</th>
                                   <th style="width: 15%" class="text-right">Debit</th>
                                   <th style="width: 15%" class="text-right">Credit</th>
                                   <th style="width: 15%" class="text-right">Closing Balance</th>
                               </tr>
                           </thead>
                           <tbody>
                               {!! $trialBalance !!}
                           </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection