@extends('pms.backend.layouts.master-layout')

@section('title', config('app.name', 'laravel'). ' | '.$title)

@section('page-css')
<style type="text/css" media="screen">
    .select2-container--default .select2-results__option[aria-disabled=true]{
        color: black !important;
        font-weight: bold !important;
    }
</style>
@endsection

@section('main-content')
<!-- WRAPPER CONTENT ----------------------------------------------------------------------------->
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
          <ul class="breadcrumb">
              <li>
                  <i class="ace-icon fa fa-home home-icon"></i>
                  <a href="#">Home</a>
              </li>
              <li>
                  <a href="#">PMS</a>
              </li>
              <li class="active">{{__($title)}}</li>
              <li class="top-nav-btn">
                <a href="{{ url('pms/return-faq') }}" class="btn btn-sm btn-primary text-white">Go Back</i></a>
            </li>
        </ul><!-- /.breadcrumb -->

    </div>

    <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <form action="{{ route('pms.return-faq.store') }}" method="post" accept-charset="utf-8">
                        @csrf

                        <div class="row">
                            <div class="col-md-3">
                                <p class="mb-1 font-weight-bold"><label for="category_id">{{ __('Sub Category') }}:</label> {!! $errors->has('category_id')? '<span class="text-danger text-capitalize">'. $errors->first('category_id').'</span>':'' !!}</p>
                                <div class="select-search-group input-group input-group-md mb-3 d-">
                                    <select name="category_id" id="category_id" class="form-control rounded select2" required>
                                        <option selected disabled value="{{ null }}">{{ __('Select One') }}</option>
                                        {!! categoryOptions([]) !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <p class="mb-1 font-weight-bold"><label for="name">{{ __('Question') }}:</label> {!! $errors->has('name')? '<span class="text-danger text-capitalize">'. $errors->first('name').'</span>':'' !!}</p>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="name" id="name" class="form-control rounded" aria-label="Large" placeholder="{{__('Write Question')}}" aria-describedby="inputGroup-sizing-sm" required value="{{ old('name') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-success btn-block"><i class="la la-check"></i>&nbsp;Save</button>
                            </div>
                            <div class="col-md-2">
                                <a class="btn btn-sm btn-danger btn-block" href="{{ url('pms/return-faq') }}"><i class="la la-ban"></i>&nbsp;Cancel</a>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
