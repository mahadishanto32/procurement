@extends('pms.backend.layouts.master-layout')
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
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul>
        </div>

        <div class="page-content">
            <div class="panel panel-info mt-3">
                <div class="panel-boby p-3">
                    <form action="{{ route('pms.product-management.attribute-options.update', $attributeOption->id) }}" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                        <div class="row">
                            <div class="col-md-3">
                                <label for="company_id"><strong>{{ __('Attributes') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <select name="attribute_id" id="attribute_id" class="form-control rounded">
                                        @if(isset($attributes[0]))
                                        @foreach($attributes as $key => $attribute)
                                        <option value="{{ $attribute->id }}" {{ $attributeOption->attribute_id == $attribute->id ? 'selected' : '' }}>{{ $attribute->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="name"><strong>{{ __('Attribute Option Name') }}:<span class="text-danger">&nbsp;*</span></strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="name" id="name" value="{{ old('name', $attributeOption->name) }}" class="form-control rounded">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="description"><strong>{{ __('Attribute Option Description') }}:</strong></label>
                                <div class="input-group input-group-md mb-3 d-">
                                    <input type="text" name="description" id="description" value="{{ old('description', $attributeOption->description) }}" class="form-control rounded">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a class="btn btn-dark btn-md" href="{{ url('pms/product-management/attribute-options') }}"><i class="la la-times"></i>&nbsp;Cancel</a>
                                <button type="submit" class="btn btn-success btn-md"><i class="la la-save"></i>&nbsp;Update Attribute</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection