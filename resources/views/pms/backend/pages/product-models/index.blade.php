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
            </ul>
        </div>

        <div class="page-content">
            <div class="panel panel-info mt-2 p-2" style="padding-bottom: 0px !important">
                <div class="row">
                    <div class="col-md-9">
                        <form action="{{ route('pms.product-management.product-models.index') }}" method="get" accept-charset="utf-8">
                            <div class="row">
                                <div class="col-md-6">
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
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-success"><i class="la la-search"></i>&nbsp;Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-3">
                        @can('attribute-option-create')
                        <a class="btn btn-sm btn-success pull-right ml-2" href="{{ url('pms/product-management/product-models/create?product_id='.request()->get('product_id')) }}" style="float: right"><i class="la la-plus"></i>&nbsp;New Product Model</a>
                        @endcan
                    </div>
                </div>
            </div>
            @if(isset($models[0]))
            <div class="panel panel-info mt-2 p-2">
                <table class="table table-bordered" cellspacing="0" width="100%" id="dataTable">
                    <thead>
                        <tr>
                           <th style="width: 5%">{{__('SL')}}</th>
                           <th style="width: 20%">{{__('Product')}}</th>
                           <th style="width: 10%">{{__('Model')}}</th>
                           <th style="width: 15%">{{__('Model Name')}}</th>
                           <th style="width: 20%">{{__('Attributes')}}</th>
                           <th style="width: 10%">{{__('Price')}}</th>
                           <th style="width: 10%">{{__('Tax (%)')}}</th>
                           <th style="width: 10%">{{__('Actions')}}</th>
                       </tr>
                   </thead>
                   <tbody>
                    @foreach($models as $key => $model)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $model->product->name }}</td>
                        <td>{{ $model->model }}</td>
                        <td>{{ $model->model_name }}</td>
                        <td>{{ $model->attributes->pluck('attributeOption.name')->implode(' - ') }}</td>
                        <td class="text-right">{{ $model->unit_price }}</td>
                        <td class="text-right">{{ $model->tax }}</td>
                        <td class="text-center">
                            @can('attribute-option-edit')
                                <a class="btn btn-xs btn-primary" href="{{ url('pms/product-management/product-models/'.$model->id.'/edit') }}"><i class="la la-edit"></i></a>
                            @endcan

                            @can('attribute-option-delete')
                            <a class="btn btn-xs btn-danger deleteBtn" data-src="{{ route('pms.product-management.product-models.destroy', $model->id) }}"><i class="la la-trash"></i></a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                   </tbody>
                </table>
            </div>
            @else
                @if(request()->has('product_id'))
                <h4 class="text-center">This Product has no model yet.</h4>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
    (function ($) {
        "use script";
        const showAlert = (status, error) => {
            swal({
                icon: status,
                text: error,
                dangerMode: true,
                buttons: {
                    cancel: false,
                    confirm: {
                        text: "OK",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                if(value)form.reset();
            });
        };

        $('.deleteBtn').on('click', function () {
            swal({
                title: "{{__('Are you sure?')}}",
                text: "{{__('Once you delete, You can not recover this data and related files.')}}",
                icon: "warning",
                dangerMode: true,
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Delete",
                        value: true,
                        visible: true,
                        closeModal: true
                    },
                },
            }).then((value) => {
                if(value){
                    var button = $(this);
                    $.ajax({
                        type: 'DELETE',
                        url: $(this).attr('data-src'),
                        dataType: 'json',
                        success:function (response) {
                            if(response.success){
                                swal({
                                    icon: 'success',
                                    text: response.message,
                                    button: false
                                });
                                setTimeout(()=>{
                                    swal.close();
                                }, 1500);
                                button.parent().parent().remove();
                            }else{
                                showAlert('error', response.message);
                                return;
                            }
                        },
                    });
                }
            });
        })
    })(jQuery)
</script>
@endsection