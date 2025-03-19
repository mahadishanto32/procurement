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
                <li>
                    <a href="#">PMS</a>
                </li>
                <li class="active">{{__($title)}}</li>
                <li class="top-nav-btn">
                   
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="panel panel-body">
                <div id="viewResult">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-head" cellspacing="0" width="100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('SL No.')}}</th>
                                    <th>{{__('PO Reference')}}</th>
                                    <th>{{__('Gate-In Reference')}}</th>
                                    <th>{{__('Gate-In Date')}}</th>
                                    <th>{{__('Po Qty')}}</th>
                                    <th>{{__('Gate-In Qty')}}</th>
                                    <th>{{__('Approved Qty')}}</th>
                                    <th>{{__('Return Qty')}}</th>
                                    <th>{{__('Replace Qty')}}</th>
                                    <th>{{__('Receive Status')}}</th>
                                    <th>{{__('GRN')}}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if(count($purchaseOrder)>0)
                                @foreach($purchaseOrder as $pkey=> $po)
                                @if($po->relGoodReceiveNote->count() > 0)
                                @foreach($po->relGoodReceiveNote as $rkey => $values)
                                <tr>
                                   
                                    @if($rkey == 0)
                                    <td rowspan="{{ $po->relGoodReceiveNote->count() }}">{{ ($purchaseOrder->currentpage()-1) * $purchaseOrder->perpage() + $pkey + 1 }}</td>

                                    <td rowspan="{{ $po->relGoodReceiveNote->count() }}">
                                        <a href="javascript:void(0)" class="btn btn-link showQCEPODetails" data-src="{{route('pms.purchase.order-list.show',$values->relPurchaseOrder->id)}}" data-title="Purchase Order Details">{{$values->relPurchaseOrder->reference_no}}
                                        </a>
                                    </td>
                                    @endif

                                    <td>
                                        <a href="javascript:void(0)" class="btn btn-link showQCEPODetails" data-src="{{route('pms.grn.grn-process.show',$values->id)}}" data-title="Gate-In Details">{{$values->reference_no}}
                                        </a>
                                    </td>
                                    <td>
                                        {{date('d-M-Y',strtotime($values->received_date))}}
                                    </td>
                                    <td>{{$values->relPurchaseOrder->relPurchaseOrderItems->sum('qty')}}</td>
                                    
                                    <td>{{$values->relGoodsReceivedItems->sum('qty')}}</td>
                                    
                                    <td>
                                            {{$values->relGoodsReceivedItems->where('quality_ensure','approved')->sum('received_qty')}}
                                        </td>
                                        <td>
                                            {{$values->relGoodsReceivedItems->where('quality_ensure','return')->sum('qty')-$values->relGoodsReceivedItems->where('quality_ensure','return')->sum('received_qty')}}
                                        </td>
                                        <td>
                                            {{$values->relGoodsReceivedItems->where('quality_ensure','return-change')->sum('qty')-$values->relGoodsReceivedItems->where('quality_ensure','return-change')->sum('received_qty')}}
                                        </td>


                                    <td class="capitalize">{{ucfirst($values->received_status)}} Received</td>
                                    
                                    <td>
                                    @if($values->relGoodsReceivedItems()->whereHas('relGoodsReceivedItemStockIn', function($query){
                                        return $query->where('is_grn_complete','no')->where('received_qty','>', 0);
                                    })->count()>0)
                                        <a href="{{route('pms.grn.stock.in.list',$values->id)}}" class="btn btn-success btn-sm">GRN</a>
                                    @endif
                                    </td>
                                </tr>

                                @endforeach
                                @endif
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="col-12 py-2">
                            @if(count($purchaseOrder)>0)
                            <ul>
                                {{$purchaseOrder->links()}}
                            </ul>

                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="POdetailsModel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="body">

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('page-script')
<script>

   (function ($) {
    "use script";
    const showQCEPODetails = () => {
        $('.showQCEPODetails').on('click', function () {

            var modalTitle= $(this).attr('data-title');
            $.ajax({
                url: $(this).attr('data-src'),
                type: 'get',
                dataType: 'json',
                data: '',
            })
            .done(function(response) {

                if (response.result=='success') {
                    $('#POdetailsModel').find('#body').html(response.body);
                    $('#POdetailsModel').find('.modal-title').html(modalTitle);
                    $('#POdetailsModel').modal('show');
                }
            })
            .fail(function(response){
                notify('Something went wrong!','error');
            });
        });
    }
    showQCEPODetails();

})(jQuery);
</script>
@endsection