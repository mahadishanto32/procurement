@extends('accounting.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .col-form-label{
        font-size: 14px;
        font-weight: 600;
    }
    .select2-container--default .select2-results__option[aria-disabled=true] {
        color: #000 !important;
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
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul>
        </div>

        <div class="page-content">
            <div class="panel panel-info mt-3">
                <div class="panel-boby p-3">
                    <form action="{{ route('accounting.bank-accounts.store') }}" method="post" accept-charset="utf-8">
                    @csrf
                        <div class="row pr-3">
                            <div class="col-md-2">
                                <label for="code"><strong>{{ __('Code') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="code" id="code" value="{{ $code }}" readonly class="form-control rounded">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="type"><strong>{{ __('Account Type') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <select name="type" id="type" class="form-control rounded">
                                        @foreach(bankAccountTypes() as $key => $type)
                                        <option value="{{ $key }}">{{ $type['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="name"><strong>{{ __('Account Name') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control rounded">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="number"><strong>{{ __('Account Number') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="number" id="number" value="{{ old('number') }}" class="form-control rounded">
                                </div>
                            </div>
                        </div>
                        <div class="row pr-3">
                            <div class="col-md-3">
                                <label for="bank_name"><strong>{{ __('Bank Name') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}" class="form-control rounded">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <label for="bank_address"><strong>{{ __('Bank Address') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="bank_address" id="bank_address" value="{{ old('bank_address') }}" class="form-control rounded">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a class="btn btn-dark btn-md" href="{{ url('accounting/bank-accounts') }}"><i class="la la-times"></i>&nbsp;Cancel</a>
                                <button type="submit" class="btn btn-success btn-md"><i class="la la-save"></i>&nbsp;Save Bank Accounts</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection