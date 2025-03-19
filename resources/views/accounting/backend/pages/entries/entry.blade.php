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
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul>
        </div>

        <div class="page-content">
            <div class="panel panel-info mt-3">
                <div class="panel-boby p-3">
                    <div class="row export-table">
                        <div class="col-md-12">
                            <div class="row pr-3 pl-2">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td style="border-top: none !important">Code:</td>
                                            <td style="border-top: none !important">Number:</td>
                                            <td style="border-top: none !important">Date:</td>
                                            <td style="border-top: none !important">Tag:</td>
                                            <td style="border-top: none !important">Fiscal Year:</td>
                                        </tr>
                                        <tr>
                                            <td style="border-top: none !important">
                                                <strong>{{ $entry->code }}</strong>
                                            </td>
                                            <td style="border-top: none !important">
                                                <strong>{{ $entry->number }}</strong>
                                            </td>
                                            <td style="border-top: none !important">
                                                <strong>{{ $entry->date }}</strong>
                                            </td>
                                            <td style="border-top: none !important">
                                                <strong>{{ $entry->tag ? $entry->tag->title : '' }}</strong>
                                            </td>
                                            <td style="border-top: none !important">
                                                <strong>{{ $entry->fiscalYear->title }}&nbsp;|&nbsp;{{ date('d-M-y', strtotime($entry->fiscalYear->start)).' to '.date('d-M-y', strtotime($entry->fiscalYear->end)) }})</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%">D/C</th>
                                                <th style="width: 15%">Cost Centre</th>
                                                <th style="width: 25%">Ledger</th>
                                                <th style="width: 15%">Debit</th>
                                                <th style="width: 15%">Credit</th>
                                                <th style="width: 20%">Narration</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($entry->items->count() > 0)
                                            @foreach($entry->items as $key => $item)
                                            <tr>
                                                <td>{{ $item->debit_credit == "D" ? "Debit" : "Credit" }}</td>
                                                <td>{{ $item->costCentre ? '['.$item->costCentre->code.'] '.$item->costCentre->name : '' }}</td>
                                                <td>{{ $item->chartOfAccount ? '['.$item->chartOfAccount->code.'] '.$item->chartOfAccount->name : '' }}</td>
                                                <td class="text-right">{{ $item->debit_credit == "D" ? $item->amount : '' }}</td>
                                                <td class="text-right">{{ $item->debit_credit == "C" ? $item->amount : '' }}</td>
                                                <td>{{ $item->narration }}</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                        @php
                                            $total_debit = $entry->items->where('debit_credit', 'D')->sum('amount');
                                            $total_credit = $entry->items->where('debit_credit', 'C')->sum('amount');
                                            $d_deference = $total_credit > $total_debit ? ($total_credit-$total_debit) : 0;
                                            $c_deference = $total_debit > $total_credit ? ($total_debit-$total_credit) : 0;
                                        @endphp
                                        <tfoot>
                                            <tr>
                                                <td colspan="3">
                                                    <h5><strong>Total</strong></h5>
                                                </td>
                                                <td style="font-weight: bold;" class="text-right total-debit {{ $d_deference > 0 || $c_deference > 0 ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $total_debit }}
                                                </td>
                                                <td style="font-weight: bold;" class="text-right total-credit {{ $d_deference > 0 || $c_deference > 0 ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $total_credit }}
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <h5><strong>Difference</strong></h5>
                                                </td>
                                                <td style="font-weight: bold;" class="text-right debit-difference">
                                                    {{ $d_deference > 0 ? $d_deference : '' }}
                                                </td>
                                                <td style="font-weight: bold;" class="text-right credit-difference">
                                                    {{ $c_deference > 0 ? $c_deference : '' }}
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><strong>Notes:</strong></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>{{ $entry->notes }}</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 pl-4">
                            @include('accounting.backend.pages.reports.buttons', [
                                'title' => $title,
                                'url' => url('accounting/entries/'.$entry->id),
                                'searchHide' => true,
                                'clearHide' => true,
                            ])
                        </div>
                        <div class="col-md-4 offset-md-2 pt-2 text-right">
                            <a class="btn btn-success btn-sm btn-submit" href="{{ url('accounting/entries/'.$entry->id.'/edit') }}"><i class="la la-edit"></i>&nbsp;Edit Entry</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
