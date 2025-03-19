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
                    <form action="{{ route('accounting.chart-of-accounts.store') }}" method="post" accept-charset="utf-8">
                    @csrf
                        <div class="row pr-3">
                            <div class="col-md-4">
                                <label for="account_group_id"><strong>{{ __('Account Group') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <select name="account_group_id" id="account_group_id" class="form-control rounded">
                                        {!! $groups !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="code"><strong>{{ __('Ledger Code') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="code" id="code" value="{{ old('code', $code) }}" class="form-control rounded">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="name"><strong>{{ __('Ledger Name') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control rounded">
                                </div>
                            </div>
                        </div>
                        <div class="row pt-3">
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <h5 class="pl-2">
                                                <label style="cursor: pointer"><input type="checkbox" name="bank_or_cash" value="1" style="transform: scale(1.5, 1.5);cursor: pointer">&nbsp;&nbsp;&nbsp;Bank or cash account</label>
                                            </h5>
                                            <p>Note: Select if the ledger account is a bank or a cash account.</p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <h5 class="pl-2">
                                                <label style="cursor: pointer"><input type="checkbox" name="reconciliation" value="1" style="transform: scale(1.5, 1.5);cursor: pointer">&nbsp;&nbsp;&nbsp;Reconciliation</label>
                                            </h5>
                                            <p>Note : If selected the ledger account can be reconciled from Reports > Reconciliation.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="col-md-12">
                                    <label for="type"><strong>{{ __('Ledger Type') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <select name="type" id="type" class="form-control rounded">
                                            <option value="D">Debit</option>
                                            <option value="C">Credit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="opening_balance"><strong>Opening Balance</strong></label>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <input type="number" step="0.01" name="opening_balance" id="opening_balance" value="{{ old('opening_balance', 0) }}" class="form-control text-right rounded">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="notes"><strong>{{ __('Notes') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                    <div class="input-group input-group-md mb-3 d-">
                                        <textarea name="notes" id="notes" class="form-control rounded" style="min-height: 120px">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a class="btn btn-dark btn-md" href="{{ url('accounting/chart-of-accounts') }}"><i class="la la-times"></i>&nbsp;Cancel</a>
                                <button type="submit" class="btn btn-success btn-md"><i class="la la-save"></i>&nbsp;Save Ledger Account</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-script')
<script type="text/javascript">
    //getCode();
    function getCode() {
        $.ajax({
            url: '{{ url('accounting/chart-of-accounts') }}/'+$('#account_group_id').val(),
            type: 'GET',
            data: {},
        })
        .done(function(code) {
            $('#code').val(code);
        });
    }
</script>
@endsection