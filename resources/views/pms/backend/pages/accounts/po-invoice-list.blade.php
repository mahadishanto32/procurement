@extends('pms.backend.layouts.master-layout')
@section('title', config('app.name', 'laravel'). ' | '.$title)
@section('page-css')
<style type="text/css">
    .list-unstyled .ratings {
        display: none;
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
                <li><a href="#">Accounts</a></li>
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                    <a href="javascript:history.back()" class="btn btn-danger btn-sm"><i class="las la-arrow-left"></i> Back</a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="panel">

                <div class="panel-body">
                    <div class="table-responsive ">
                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('SL No.')}}</th>
                                    <th>{{__('P.O Reference')}}</th>
                                    <th>{{__('P.O Date')}}</th>
                                    <th>{{__('GRN Reference')}}</th>
                                    <th>{{__('GRN Date')}}</th>
                                    <th>{{__('Challan No')}}</th>
                                    <th>{{__('Challan File')}}</th>
                                    <th  class="text-center" class="text-center">{{__('Po Qty')}}</th>
                                    <th  class="text-center">{{__('GRN Qty')}}</th>
                                    <th  class="text-center">{{__('GRN Amount')}}</th>
                                    <th class="text-center">{{__('Bill Amount')}}</th>
                                    <th>{{__('Receive Status')}}</th>
                                    <th class="text-center">{{__('Invoice')}}</th>
                                    <th class="text-center">{{__('Vat')}}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if(isset($purchaseOrder))
                                @if($purchaseOrder->relGoodReceiveNote->count() > 0)
                                @foreach($purchaseOrder->relGoodReceiveNote as $key=>$grn)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    @if($key==0)
                                    <td rowspan="{{$purchaseOrder->relGoodReceiveNote->count()}}">
                                        <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.purchase.order-list.show',$purchaseOrder->id)}}" data-title="Purchase Order Details">{{$purchaseOrder->reference_no}}
                                        </a>
                                    </td>
                                    @endif

                                    <td>
                                        {{date('d-M-Y',strtotime($purchaseOrder->po_date))}}
                                    </td>

                                    <td>
                                        <a href="javascript:void(0)" class="btn btn-link showPODetails" data-src="{{route('pms.grn.grn-process.show',$grn->id)}}" data-title="GRN Details">{{$grn->grn_reference_no}}
                                        </a>
                                    </td>

                                    <td>
                                        {{date('d-M-Y',strtotime($grn->received_date))}}
                                    </td>
                                    <td>{{$grn->challan}}</td>
                                    <td>
                                        @if(!empty($grn->challan_file))
                                        <a href="{{asset($grn->challan_file)}}" class="btn btn-info btn-xs">
                                        <i class="las la-eye">View</i></a>
                                        @endif
                                    </td>
                                    <td>{{$purchaseOrder->relPurchaseOrderItems->sum('qty')}}</td>
                                    <td>{{$grn->relGoodsReceivedItems->sum('qty')}}</td>
                                    <?php 
                                    $goodsReceiveItemsId=$goodsReceivedItem->where('goods_received_note_id',$grn->id)
                                    ->pluck('id')
                                    ->all();
                                    ?>
                                    <td>
                                        @if($goodsReceiveItemsId)
                                        {{number_format($purchaseOrder->relGoodsReceivedItemStockIn()
                                            ->whereIn('goods_received_item_id',$goodsReceiveItemsId)
                                            ->where('is_grn_complete','yes')
                                            ->sum('total_amount'),2)}}
                                            @endif
                                    </td>

                                    @php 
                                        $po_attachment=$poAttachment->where('purchase_order_id',$purchaseOrder->id)
                                        ->where('goods_received_note_id',$grn->id)
                                        ->where('bill_type','grn')
                                        ->first();
                                    @endphp

                                    <td class="text-center">{{isset($po_attachment)?number_format($po_attachment->bill_amount,2):'Not Updated Yet.'}}</td>
                                    <td class="capitalize">{{$grn->received_status}}</td>

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
                                </tr>
                                @endforeach
                                @endif
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection