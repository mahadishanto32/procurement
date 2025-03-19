@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .col-form-label{
        font-size: 14px;
        font-weight: 600;
    }

    .bordered{
        border: 1px #ccc solid
    }
    .floating-title{
        position: absolute;
        top: -13px;
        left: 15px;
        background: white;
        padding: 0px 5px 5px 5px;
        font-weight: 500;
    }
    .card-body{
        padding-top: 20px !important;
        padding-bottom: 0px !important;
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
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul>
        </div>

        <div class="page-content">
            <div class="panel panel-info mt-3">
                <div class="panel-boby p-3">
                    <form action="{{ route('pms.product-management.product-models.store') }}" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    @csrf
                        <div class="row mt-2 mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body bordered">
                                        <h5 class="floating-title">Product Model Information</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="product_id"><strong>{{ __('Products') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <select name="product_id" id="product_id" class="form-control rounded">
                                                        @if(isset($products[0]))
                                                        @foreach($products as $key => $product)
                                                        <option value="{{ $product->id }}" {{ request()->get('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="model"><strong>{{ __('Model No.') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="text" name="model" id="model" value="{{ old('model') }}" class="form-control rounded">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <label for="model_name"><strong>{{ __('Model Name') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="text" name="model_name" id="model_name" value="{{ old('model_name') }}" class="form-control rounded">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="unit_price"><strong>{{ __('Unit Price') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="number" step="0.01" min="0" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" class="form-control rounded">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="tax"><strong>{{ __('Tax (%)') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="number" step="0.01" min="0" name="tax" id="tax" value="{{ old('tax') }}" class="form-control rounded">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="description"><strong>{{ __('Description') }}:</strong></label>
                                                <div class="input-group input-group-md mb-3 d-">
                                                    <input type="text" name="description" id="description" value="{{ old('description') }}" class="form-control rounded">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4 mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body bordered">
                                        <h5 class="floating-title">Attributes</h5>
                                        <div class="row">
                                            @if(isset($attributes[0]))
                                            @foreach($attributes as $key => $attribute)
                                                <div class="col-md-2 mb-3">
                                                    <label for="attribute-{{ $attribute->id }}"><strong>{{ $attribute->name }}:</strong></label>
                                                    <div class="input-group input-group-md mb-3 d-">
                                                        <select name="attribute_option_id[]" id="attribute-{{ $attribute->id }}" class="form-control rounded">
                                                            <option value="0">Not Required</option>
                                                            @if(isset($attribute->options[0]))
                                                            @foreach($attribute->options as $key => $option)
                                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div> 
                                                </div>
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a class="btn btn-dark btn-md" href="{{ url('pms/product-management/product-models?product_id='.request()->get('product_id')) }}"><i class="la la-times"></i>&nbsp;Cancel</a>
                                <button type="submit" class="btn btn-success btn-md"><i class="la la-save"></i>&nbsp;Save Product Model</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection