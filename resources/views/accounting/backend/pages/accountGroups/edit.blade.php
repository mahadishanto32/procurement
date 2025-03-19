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
                    <form action="{{ route('accounting.account-groups.update', $group->id) }}" method="post" accept-charset="utf-8">
                    @csrf
                    @method('PUT')
                        <div class="row pr-3">
                            <div class="col-md-4">
                                <label for="parent_id"><strong>{{ __('Parent Group') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <select name="parent_id" id="parent_id" class="form-control rounded">
                                        {!! $accountGroupOptions !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="code"><strong>{{ __('Group Code') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="code" id="code" class="form-control rounded" value="{{ $group->code }}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="name"><strong>{{ __('Group Name') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}" class="form-control rounded">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a class="btn btn-dark btn-md" href="{{ url('accounting/chart-of-accounts') }}"><i class="la la-times"></i>&nbsp;Cancel</a>
                                <button type="submit" class="btn btn-success btn-md"><i class="la la-save"></i>&nbsp;Update Account Group</button>
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
            url: '{{ url('accounting/account-groups') }}/'+$('#parent_id').val()+"?group_id={{ $group->id }}",
            type: 'GET',
            data: {},
        })
        .done(function(code) {
            $('#code').val(code);
        });
    }
</script>
@endsection