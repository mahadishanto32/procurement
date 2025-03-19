@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .col-form-label{
        font-size: 14px;
        font-weight: 600;
    }

    .list-unstyled .ratings {
        display: none;
    }
</style>
@endsection
@section('main-content')
@php
use App\Models\PmsModels\Purchase\PurchaseOrderAttachment;
use App\Models\PmsModels\SupplierPayment;
@endphp
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
                <li class="active">Accounts</li>
                <li class="active">{{__($title)}}</li>

            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="panel">
                <div class="table-responsive panel panel-body">
                    <table class="table table-striped table-bordered table-head datatable-exportable" data-table-name="{{ $title }}" cellspacing="0" width="100%" id="dataTable">
                        <thead>
                            <tr>
                               <th width="5%">{{__('SL No.')}}</th>
                               <th>{{__('P.O. Date')}}</th>
                               <th>{{__('Supplier')}}</th>
                               <th>{{__('Reference No')}}</th>
                               <th>{{__('P.O Qty')}}</th>
                               <th class="text-center">{{__('Po Amount')}}</th>
                               <th class="text-center">{{__('GRN Qty')}}</th>
                               <th class="text-center">{{__('GRN Amount')}}</th>
                               <th class="text-center">{{__('Bill Amount')}}</th>
                               <th class="text-center">{{__('Paid Amount')}}</th>
                               <th class="text-center">{{__('Status')}}</th>
                               <th class="text-center">{{__('Invoice')}}</th>
                               <th class="text-center">{{__('Vat')}}</th>
                               <th class="text-center">{{__('Option')}}</th>
                           </tr>
                       </thead>
                       <tbody id="viewResult">
                        @if(count($purchase_order)>0)
                        @foreach($purchase_order as $key=> $values)
                        @php
                        $po_attachment=PurchaseOrderAttachment::where('purchase_order_id',$values->id)->where('bill_type','po')->first();
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{date('d-m-Y',strtotime($values->po_date))}}</td>
                            <td>{{$values->relQuotation?$values->relQuotation->relSuppliers->name:''}}</td>
                            <td> <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$values->id)}}">{{$values->reference_no}}</a></td>

                            <td>{{$values->relPurchaseOrderItems->sum('qty')}}</td>
                            <td class="text-center">{{number_format($values->gross_price,2)}}</td>
                            <td class="text-center">{{$values->total_grn_qty}}</td>
                            <td class="text-center">
                                @if($values->relGoodsReceivedItemStockIn)

                                {{number_format($values->relGoodsReceivedItemStockIn()->where('is_grn_complete','yes')->sum('total_amount'),2)}}
                                @endif
                            </td>
                            <td class="text-center">{{PurchaseOrderAttachment::where('purchase_order_id',$values->id)->sum('bill_amount') > 0 ? number_format(PurchaseOrderAttachment::where('purchase_order_id',$values->id)->sum('bill_amount'), 2)  : 'Not Updated Yet'}}</td>
                            <td class="text-center">{{ number_format(SupplierPayment::where('purchase_order_id',$values->id)->sum('pay_amount'), 2) }}</td>

                            <td class="text-center text-left">
                                @if($values->relPurchaseOrderItems->sum('qty')==$values->total_grn_qty??0)
                                {{__('Full Received')}}
                                @else
                                {{__('Partial Received')}}
                                @endif
                            </td>
                            <td class="text-center">
                                @if(isset($po_attachment))
                                <a href="{{asset($po_attachment->invoice_file)}}" target="__blank" class="btn btn-success btn-xs">
                                    <i class="las la-eye">Invoice</i>
                                </a>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(isset($po_attachment))
                                <a href="{{asset($po_attachment->vat_challan_file)}}" class="btn btn-success btn-xs" target="__blank">
                                    <i class="las la-eye">Vat</i>
                                </a>
                                @endif
                            </td>
                            
                            <td class="text-center">
                                <a href="{{route('pms.accounts.po.invoice.list',$values->id)}}" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Click here to PO Challan List">Challan({{$values->relGoodReceiveNote->count()}}) </a>

                                <a target="__blank" href="{{route('pms.billing-audit.po.invoice.print',$values->id)}}" class="btn btn-warning btn-xs"><i class="las la-print"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-12">
                        <div class="la-1x pull-right">
                            @if(count($purchase_order)>0)
                            <ul>
                                {{$purchase_order->links()}}
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection