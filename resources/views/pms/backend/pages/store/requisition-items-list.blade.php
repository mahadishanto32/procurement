@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
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
                <li>
                    <a href="#">PMS</a>
                </li>
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-sm btn-warning text-white" data-toggle="tooltip" title="Back" > <i class="las la-chevron-left"></i>Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h5>Requisition Details</h5>
                        
                    </div>
                    <div class="panel-body">
                        
                        
                        <form  method="post" id="updateInventoryForm" action="{{ route('pms.store-manage.notification.send.to.users') }}">
                            @csrf

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Category</th>
                                            <th>Product</th>
                                            <th>Unit</th>
                                            <th>Stock Qty</th>
                                            <th>Req.Qty</th>
                                            <th>Option</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if(isset($requisition))
                                        @php 
                                        $total_stock_qty = 0;
                                        $total_requision_qty = 0;
                                        @endphp
                                        @foreach($requisition->items as $key=>$item)

                                        @if(isset($item->product->relInventorySummary) && $item->product->relInventorySummary->qty >= $item->qty)
                                        <tr id="SelectedRow{{$item->product->id}}">
                                            <td>{{$key+1}}</td>
                                            <td>{{$item->product->category->name}}</td>
                                            <td>{{$item->product->name}} ({{ getProductAttributes($item->product_id) }})</td>

                                            <td>{{($item->product->productUnit->unit_name)?$item->product->productUnit->unit_name:''}}</td>
                                            <td>{{isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0}}</td>
                                            <td>{{$item->qty}}</td>

                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="items_id[]" id="item_{{$item->id}}" value="{{$item->id}}" title="Click here to send requisition" {{isset($item->notification->requisition_item_id)?'checked':''}}
                                                    {{isset($item->notification->requisition_item_id)?'disabled':''}} >
                                                    <label class="form-check-label" for="item_{{$item->id}}">
                                                        <strong>{{ isset($item->notification->requisition_item_id)?'Already Send':'Send Notification'}}</strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        @php
                                        $total_stock_qty += isset($item->product->relInventorySummary->qty)?$item->product->relInventorySummary->qty:0;
                                        $total_requision_qty += $item->qty;
                                        @endphp
                                        @endif
                                        @endforeach
                                        @endif
                                        <tr>
                                            <td colspan="4" class="text-right">Total</td>
                                            <td colspan="">{{$total_stock_qty}}</td>
                                            <td colspan="">{{$total_requision_qty}}</td>
                                            <td colspan=""><button style="margin-right:20px;" type="submit" class="btn btn-success rounded btn-sm">{{ __('Send') }}</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-row">
                                <input type="hidden" name="requisition_id" value="{{$requisition->id}}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-script')

@endsection